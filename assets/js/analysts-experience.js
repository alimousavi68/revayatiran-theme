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
      ? '<img src="' + escapeHtml(safeUrl(item.author_avatar)) + '" alt="' + escapeHtml(item.author_name) + '" loading="lazy" decoding="async" width="44" height="44">'
      : '<span class="anx-post-card__avatar-placeholder" aria-hidden="true"><i class="ph ph-user"></i></span>';
    var media = item.thumbnail_url
      ? '<a class="anx-post-card__media" href="' + escapeHtml(postUrl) + '" tabindex="-1" aria-hidden="true"><img src="' + escapeHtml(safeUrl(item.thumbnail_url)) + '" alt="" loading="lazy" decoding="async" width="800" height="450"></a>'
      : '';
    var score = Number(item.score) > 0 ? faNumber(item.score, 1) + ' از ۵' : 'بدون امتیاز';
    var authorRole = item.author_role ? '<small>' + escapeHtml(item.author_role) + '</small>' : '';
    var date = item.date ? '<time>' + escapeHtml(item.date) + '</time>' : '';

    article.className = 'an-card--grid anx-post-card';
    article.setAttribute('data-post-id', String(Number(item.id) || ''));
    article.innerHTML =
      '<div class="anx-post-card__content' + (media ? '' : ' has-no-media') + '">' +
      '<div class="anx-post-card__copy">' +
      '<a class="anx-post-card__author" href="' + escapeHtml(authorUrl) + '">' + avatar + '<span><strong>' + escapeHtml(item.author_name) + '</strong>' + authorRole + date + '</span></a>' +
      '<h3><a href="' + escapeHtml(postUrl) + '">' + escapeHtml(item.title) + '</a></h3>' +
      (item.excerpt ? '<p>' + escapeHtml(item.excerpt) + '</p>' : '') + '</div>' + media + '</div>' +
      '<footer class="anx-post-card__actions"><span title="' + (Number(item.votes_count) > 0 ? faNumber(item.votes_count) + ' رأی' : 'امتیاز اولیه؛ بدون رأی کاربران') + '"><i class="ph ph-chart-bar"></i>' + escapeHtml(score) + '</span>' +
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

  // ─── 1-CLICK STAR RATING WIDGET ───────────────────────────────────────────
  function initAnalystStarRating() {
    var widget = document.querySelector('[data-anx-rating-widget]');
    if (!widget) return;

    var postId = widget.getAttribute('data-post-id');
    var nonce = widget.getAttribute('data-nonce');
    var endpoint = widget.getAttribute('data-endpoint') || '/wp-admin/admin-ajax.php';
    var canRate = widget.getAttribute('data-can-rate') === '1';
    var isLoggedIn = widget.getAttribute('data-is-logged-in') === '1';
    var currentRating = Number(widget.getAttribute('data-user-rating')) || 0;
    var starButtons = Array.prototype.slice.call(widget.querySelectorAll('.anx-star-btn'));
    var feedback = widget.querySelector('.anx-rating-feedback');
    var statsContainer = widget.querySelector('[data-rating-stats]');

    function renderStars(activeCount, isHover) {
      starButtons.forEach(function (btn) {
        var val = Number(btn.getAttribute('data-rating-value')) || 0;
        var icon = btn.querySelector('i');
        if (val <= activeCount) {
          btn.classList.add(isHover ? 'is-hover' : 'is-active');
          if (!isHover) btn.classList.remove('is-hover');
          if (icon) icon.className = 'ph-fill ph-star';
        } else {
          btn.classList.remove('is-active', 'is-hover');
          if (icon) icon.className = 'ph ph-star';
        }
      });
    }

    starButtons.forEach(function (btn) {
      var val = Number(btn.getAttribute('data-rating-value')) || 0;

      btn.addEventListener('mouseenter', function () {
        if (!canRate && isLoggedIn) return;
        renderStars(val, true);
      });

      btn.addEventListener('focus', function () {
        if (!canRate && isLoggedIn) return;
        renderStars(val, true);
      });

      btn.addEventListener('click', async function (e) {
        e.preventDefault();
        if (!isLoggedIn) {
          var loginHint = widget.querySelector('.anx-rating-login-hint');
          if (loginHint) window.location.href = loginHint.href;
          return;
        }
        if (!canRate) return;

        widget.classList.add('is-submitting');
        starButtons.forEach(function (b) { b.disabled = true; });

        try {
          var formData = new URLSearchParams();
          formData.append('action', 'revayat_vote_analyst_post');
          formData.append('post_id', postId);
          formData.append('rating', String(val));
          formData.append('revayat_vote_nonce', nonce);

          var response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: formData.toString()
          });

          var json = await response.json();
          if (json && json.success) {
            currentRating = val;
            widget.setAttribute('data-user-rating', String(val));
            renderStars(currentRating, false);

            if (statsContainer && json.data) {
              var scoreFa = faNumber(json.data.score || json.data.average, 1);
              var countFa = faNumber(json.data.count, 0);
              statsContainer.innerHTML = 'میانگین <strong data-score-val>' + scoreFa + '</strong> از ۵ (' + '<span data-count-val>' + countFa + '</span> رأی)';
            }

            if (feedback) {
              feedback.innerHTML = '<span class="anx-rating-notice is-success">' +
                'امتیاز شما: ' + faNumber(val, 0) + ' ستاره (برای تغییر دوباره کلیک کنید)' +
                '</span>';
            }
          } else {
            var errMsg = (json && json.data && json.data.message) ? json.data.message : 'خطا در ثبت امتیاز';
            if (feedback) {
              feedback.innerHTML = '<span class="anx-rating-notice is-error">' + escapeHtml(errMsg) + '</span>';
            }
            renderStars(currentRating, false);
          }
        } catch (err) {
          if (feedback) {
            feedback.innerHTML = '<span class="anx-rating-notice is-error">خطا در برقراری ارتباط با سرور.</span>';
          }
          renderStars(currentRating, false);
        } finally {
          widget.classList.remove('is-submitting');
          starButtons.forEach(function (b) { b.disabled = false; });
        }
      });
    });

    var starsGroup = widget.querySelector('.anx-stars-rating');
    if (starsGroup) {
      starsGroup.addEventListener('mouseleave', function () {
        renderStars(currentRating, false);
      });
    }
  }

  // ─── AUTHOR BIO EXPANDABLE TOGGLE ─────────────────────────────────────────
  function initAuthorBioToggle() {
    var wrap = document.querySelector('[data-anx-bio-expandable]');
    if (!wrap) return;

    var bio = wrap.querySelector('.anx-author-card__bio');
    var btn = wrap.querySelector('.anx-author-card__bio-toggle');
    if (!bio || !btn) return;

    if (bio.scrollHeight > bio.clientHeight + 4 || bio.textContent.trim().length > 180) {
      btn.hidden = false;
      btn.addEventListener('click', function () {
        var isExpanded = wrap.classList.toggle('is-expanded');
        btn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        var label = btn.querySelector('span');
        if (label) {
          label.textContent = isExpanded ? 'بستن' : 'مشاهده بیشتر';
        }
      });
    }
  }

  // ─── UNIFIED ENGAGEMENT TOOLS (COPY & SHARE) ──────────────────────────────
  function initUnifiedEngagementTools() {
    document.querySelectorAll('[data-anx-copy-btn]:not([data-anx-ready])').forEach(function (btn) {
      btn.setAttribute('data-anx-ready', 'true');
      btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-url') || window.location.href;
        var label = btn.querySelector('span');
        var original = label ? label.textContent : '';
        copyText(url).then(function () {
          if (label) {
            label.textContent = 'کپی شد!';
            window.setTimeout(function () { label.textContent = original; }, 2000);
          }
        }).catch(function () {
          if (label) {
            label.textContent = 'خطا در کپی';
            window.setTimeout(function () { label.textContent = original; }, 2000);
          }
        });
      });
    });

    document.querySelectorAll('[data-anx-native-share]:not([data-anx-ready])').forEach(function (btn) {
      btn.setAttribute('data-anx-ready', 'true');
      btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-url') || window.location.href;
        var title = btn.getAttribute('data-title') || document.title;
        if (navigator.share) {
          navigator.share({ title: title, url: url }).catch(function (err) {
            if (err && err.name !== 'AbortError') {
              copyText(url);
            }
          });
        } else {
          var label = btn.querySelector('span');
          var original = label ? label.textContent : '';
          copyText(url).then(function () {
            if (label) {
              label.textContent = 'کپی شد!';
              window.setTimeout(function () { label.textContent = original; }, 2000);
            }
          });
        }
      });
    });
  }

  initAnalystStarRating();
  initAuthorBioToggle();
  initUnifiedEngagementTools();
}());
