(function () {
  'use strict';

  function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    }
    return new Promise(function (resolve, reject) {
      var input = document.createElement('textarea');
      input.value = text;
      input.setAttribute('readonly', '');
      input.style.position = 'fixed';
      input.style.opacity = '0';
      document.body.appendChild(input);
      input.select();
      try {
        document.execCommand('copy') ? resolve() : reject(new Error('copy failed'));
      } catch (error) {
        reject(error);
      }
      input.remove();
    });
  }

  function bindShareButtons(root) {
    (root || document).querySelectorAll('[data-anx-share]:not([data-anx-share-ready])').forEach(function (button) {
      button.setAttribute('data-anx-share-ready', 'true');
      button.addEventListener('click', function () {
      var url = button.getAttribute('data-share-url') || window.location.href;
      var title = button.getAttribute('data-share-title') || document.title;
      var label = button.querySelector('span');
      var original = label ? label.textContent : '';
      var feedback = function (text) {
        if (!label) return;
        label.textContent = text;
        window.setTimeout(function () { label.textContent = original; }, 1800);
      };

      if (navigator.share) {
        navigator.share({ title: title, url: url }).catch(function (error) {
          if (error && error.name !== 'AbortError') {
            copyText(url).then(function () { feedback('کپی شد'); });
          }
        });
        return;
      }
      copyText(url).then(function () { feedback('کپی شد'); }).catch(function () { feedback('کپی نشد'); });
      });
    });
  }

  bindShareButtons(document);

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, function (character) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character];
    });
  }

  function safeUrl(value) {
    try {
      var url = new URL(String(value || ''), window.location.origin);
      return ['http:', 'https:'].indexOf(url.protocol) !== -1 ? url.href : '#';
    } catch (error) {
      return '#';
    }
  }

  function faNumber(value, maximumFractionDigits) {
    return new Intl.NumberFormat('fa-IR', { maximumFractionDigits: maximumFractionDigits || 0 }).format(Number(value) || 0);
  }

  function renderAnalystCard(item) {
    var article = document.createElement('article');
    var postUrl = safeUrl(item.url);
    var authorUrl = safeUrl(item.author_url);
    var avatar = item.author_avatar
      ? '<img src="' + escapeHtml(safeUrl(item.author_avatar)) + '" alt="' + escapeHtml(item.author_name) + '" loading="lazy" decoding="async" width="64" height="64">'
      : '<span class="anx-post-card__avatar-placeholder" aria-hidden="true"><i class="ph ph-user"></i></span>';
    var media = item.thumbnail_url
      ? '<a class="anx-post-card__media" href="' + escapeHtml(postUrl) + '" tabindex="-1" aria-hidden="true"><img src="' + escapeHtml(safeUrl(item.thumbnail_url)) + '" alt="" loading="lazy" decoding="async" width="800" height="450"></a>'
      : '';
    var field = item.field_name
      ? (item.field_url ? '<a class="anx-topic-chip" href="' + escapeHtml(safeUrl(item.field_url)) + '">' + escapeHtml(item.field_name) + '</a>' : '<span class="anx-topic-chip">' + escapeHtml(item.field_name) + '</span>')
      : '';
    var score = Number(item.votes_count) > 0 ? faNumber(item.score, 1) + ' از ۵' : 'بدون امتیاز';
    var authorRole = item.author_role ? '<small>' + escapeHtml(item.author_role) + '</small>' : '';
    var date = item.date ? '<time>' + escapeHtml(item.date) + '</time>' : '';

    article.className = 'an-card--grid anx-post-card';
    article.setAttribute('data-post-id', String(Number(item.id) || ''));
    article.innerHTML = '<header class="anx-post-card__author-row">' +
      '<a class="anx-post-card__author" href="' + escapeHtml(authorUrl) + '">' + avatar + '<span><strong>' + escapeHtml(item.author_name) + '</strong>' + authorRole + date + '</span></a>' +
      '<span class="anx-post-card__type">' + escapeHtml(item.analysis_type || 'یادداشت تحلیلی') + '</span></header>' +
      '<div class="anx-post-card__content' + (media ? '' : ' has-no-media') + '"><div class="anx-post-card__copy"><h3><a href="' + escapeHtml(postUrl) + '">' + escapeHtml(item.title) + '</a></h3>' +
      (item.excerpt ? '<p>' + escapeHtml(item.excerpt) + '</p>' : '') + field + '</div>' + media + '</div>' +
      '<footer class="anx-post-card__actions"><span><i class="ph ph-star"></i>' + escapeHtml(score) + '</span>' +
      '<a href="' + escapeHtml(postUrl) + '#comments"><i class="ph ph-chat-circle"></i>' + faNumber(item.comments_count) + ' نظر</a>' +
      '<button type="button" class="anx-post-card__share" data-anx-share data-share-url="' + escapeHtml(postUrl) + '" data-share-title="' + escapeHtml(item.title) + '"><i class="ph ph-share-network"></i><span>اشتراک</span></button>' +
      '<a class="anx-post-card__read" href="' + escapeHtml(postUrl) + '">مطالعه یادداشت<i class="ph ph-arrow-left"></i></a></footer>';
    return article;
  }

  function initAnalystAjaxFeed() {
    var feed = document.querySelector('[data-anx-ajax-feed]');
    if (!feed) return;
    var list = feed.querySelector('[data-anx-feed-list]');
    var control = feed.querySelector('[data-anx-ajax-pagination]');
    var button = feed.querySelector('[data-anx-load-more]');
    var sentinel = feed.querySelector('[data-anx-feed-sentinel]');
    var status = feed.querySelector('[data-anx-feed-status]');
    if (!list || !control || !button || !sentinel || !window.fetch) return;

    var currentPage = Number(feed.getAttribute('data-current-page')) || 1;
    var maxPages = Number(feed.getAttribute('data-max-pages')) || 1;
    var autoThrough = Number(feed.getAttribute('data-auto-through')) || 5;
    var loading = false;
    var observer = null;
    var seen = new Set(Array.prototype.map.call(list.querySelectorAll('[data-post-id]'), function (card) { return card.getAttribute('data-post-id'); }));

    function pageUrl(page) {
      var url = new URL(window.location.href);
      url.pathname = url.pathname.replace(/\/page\/\d+\/?$/, '/').replace(/\/$/, '') + '/page/' + page + '/';
      return url.href;
    }

    function syncControl() {
      if (button.hasAttribute('data-ajax-failed')) {
        button.hidden = false;
        sentinel.hidden = true;
        if (observer) observer.disconnect();
        return;
      }
      if (currentPage >= maxPages) {
        button.hidden = true;
        sentinel.hidden = true;
        if (observer) observer.disconnect();
        status.textContent = 'همه تحلیل‌ها نمایش داده شده‌اند.';
        return;
      }
      button.href = pageUrl(currentPage + 1);
      button.hidden = currentPage < autoThrough && !!observer;
      sentinel.hidden = currentPage >= autoThrough;
    }

    async function loadNext() {
      if (loading || currentPage >= maxPages) return;
      loading = true;
      list.setAttribute('aria-busy', 'true');
      control.classList.add('is-loading');
      status.textContent = 'در حال بارگذاری تحلیل‌های بیشتر…';
      try {
        var endpoint = new URL(feed.getAttribute('data-endpoint'), window.location.origin);
        endpoint.searchParams.set('page', String(currentPage + 1));
        endpoint.searchParams.set('field', feed.getAttribute('data-field') || 'all');
        endpoint.searchParams.set('sort', feed.getAttribute('data-sort') || 'date');
        endpoint.searchParams.set('search', feed.getAttribute('data-search') || '');
        var response = await fetch(endpoint.href, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        var payload = await response.json();
        var fragment = document.createDocumentFragment();
        var added = 0;
        (payload.items || []).forEach(function (item) {
          var id = String(Number(item.id) || '');
          if (!id || seen.has(id)) return;
          seen.add(id);
          fragment.appendChild(renderAnalystCard(item));
          added += 1;
        });
        list.appendChild(fragment);
        bindShareButtons(list);
        currentPage = Number(payload.current_page) || currentPage + 1;
        maxPages = Number(payload.max_pages) || maxPages;
        feed.setAttribute('data-current-page', String(currentPage));
        status.textContent = added ? faNumber(added) + ' تحلیل دیگر بارگذاری شد.' : 'تحلیل تازه‌ای در این صفحه نبود.';
        button.removeAttribute('data-ajax-failed');
      } catch (error) {
        status.textContent = 'بارگذاری خودکار انجام نشد؛ از دکمه ادامه استفاده کنید.';
        button.hidden = false;
        button.setAttribute('data-ajax-failed', 'true');
      } finally {
        loading = false;
        list.removeAttribute('aria-busy');
        control.classList.remove('is-loading');
        syncControl();
      }
    }

    button.addEventListener('click', function (event) {
      if (button.hasAttribute('data-ajax-failed')) return;
      event.preventDefault();
      loadNext();
    });

    feed.classList.add('is-enhanced');
    if ('IntersectionObserver' in window) {
      observer = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; }) && currentPage < autoThrough) loadNext();
      }, { rootMargin: '500px 0px', threshold: 0 });
      observer.observe(sentinel);
    }
    syncControl();
  }

  initAnalystAjaxFeed();

  var toc = document.querySelector('[data-anx-toc]');
  var article = document.querySelector('.anx-article-body');
  if (toc && article) {
    var headings = Array.prototype.slice.call(article.querySelectorAll('h2, h3'));
    if (headings.length >= 2) {
      var list = toc.querySelector('ol');
      headings.forEach(function (heading, index) {
        if (!heading.id) heading.id = 'analysis-section-' + (index + 1);
        var item = document.createElement('li');
        if (heading.tagName.toLowerCase() === 'h3') item.className = 'is-h3';
        var link = document.createElement('a');
        link.href = '#' + heading.id;
        link.textContent = heading.textContent;
        item.appendChild(link);
        list.appendChild(item);
      });
      toc.hidden = false;
    }
  }

  var dashboard = document.querySelector('.anx-dashboard-page');
  if (dashboard && 'IntersectionObserver' in window) {
    var links = Array.prototype.slice.call(dashboard.querySelectorAll('.anx-dashboard-nav nav a[href^="#"]'));
    var targets = links.map(function (link) { return document.querySelector(link.getAttribute('href')); }).filter(Boolean);
    var activate = function (id) {
      links.forEach(function (link) {
        var active = link.getAttribute('href') === '#' + id;
        link.classList.toggle('is-active', active);
        if (active) link.setAttribute('aria-current', 'location');
        else link.removeAttribute('aria-current');
      });
    };
    var observer = new IntersectionObserver(function (entries) {
      var visible = entries.filter(function (entry) { return entry.isIntersecting; }).sort(function (a, b) { return b.intersectionRatio - a.intersectionRatio; });
      if (visible[0]) activate(visible[0].target.id);
    }, { rootMargin: '-18% 0px -65% 0px', threshold: [0, 0.2, 0.5] });
    targets.forEach(function (target) { observer.observe(target); });
  }
}());
