/** Deterministic CSS delivery build. Run: node assets/css/build-css.mjs [--check] */
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const cssRoot = path.dirname(fileURLToPath(import.meta.url));
const themeRoot = path.resolve(cssRoot, '../..');
const out = path.join(cssRoot, 'dist');
const check = process.argv.includes('--check');
const read = file => fs.readFileSync(path.join(themeRoot, file), 'utf8');
const index = read('assets/css/main-custom.css');
const imports = [...index.matchAll(/@import\s+"\.\/modules\/([^";]+)"\s*;/g)].map(m => m[1]);
if ((index.match(/@import\b/g) || []).length !== imports.length || new Set(imports).size !== imports.length) {
  throw new Error('The source index must contain unique, local module imports in the supported format');
}
const tail = index.replace(/\/\*[\s\S]*?\*\//g, '').replace(/@import[^;]+;/g, '').trim();
const shared = imports.filter(name => !['hero.css', 'daily-narrative.css', 'news-monitoring.css',
  'special-dossiers.css', 'media-observatory.css', 'analysts-network.css',
  'analysts-mobile.css', 'situation-room.css'].includes(name));
const analysts = ['analysts-network.css', 'analysts-mobile.css'];
const experience = ['analysts-experience.css', 'analysts-refinement.css'];
const portal = ['user-portal.css', 'user-portal-otp.css', 'user-portal-profile.css', 'user-portal-workflow.css'];
const profiles = {
  public: [],
  home: ['hero.css', 'daily-narrative.css', 'news-monitoring.css', 'special-dossiers.css',
    'situation-room.css', 'media-observatory.css', ...analysts],
  post: ['dossier-story.css', 'special-dossiers.css'],
  dossier: ['dossier-story.css', 'special-dossiers.css'],
  'dossier-archive': ['special-dossiers.css', 'archive-modernization.css', 'public-archives.css'],
  analyst: ['dossier-story.css', 'media-observatory.css', ...analysts, ...experience],
  'analyst-archive': [...analysts, 'archive-modernization.css', ...experience],
  'author-archive': [...analysts],
  person: ['dossier-story.css', ...analysts, 'vertical-content.css', ...experience],
  media: ['dossier-story.css', 'vertical-content.css'],
  'media-archive': ['vertical-content.css', 'public-archives.css'],
  observatory: ['dossier-story.css', 'media-observatory.css'],
  'observatory-archive': ['media-observatory.css', 'public-archives.css'],
  room: ['dossier-story.css', 'situation-room.css'],
  'room-archive': ['situation-room.css', 'archive-modernization.css', 'public-archives.css'],
  archive: ['public-archives.css'],
  auth: [...portal],
  dashboard: [...analysts, ...portal, 'member-portal.css', ...experience],
};

/** Token-safe compaction: keep meaningful spaces, strings and calc operators. */
function compact(css) {
  let result = '', i = 0;
  while (i < css.length) {
    const c = css[i];
    if (c === '\\' && i + 1 < css.length) {
      result += css.slice(i, i + 2); i += 2;
    } else if (c === '"' || c === "'") {
      const quote = c; result += c; i++;
      let closed = false;
      while (i < css.length) {
        const next = css[i++]; result += next;
        if (next === '\\' && i < css.length) result += css[i++];
        else if (next === quote) { closed = true; break; }
      }
      if (!closed) throw new Error('Unterminated CSS string');
    } else if (c === '/' && css[i + 1] === '*') {
      const end = css.indexOf('*/', i + 2);
      if (end < 0) throw new Error('Unterminated CSS comment');
      // Preserve license comments and separate tokens formerly split by a comment.
      result += css[i + 2] === '!' ? css.slice(i, end + 2) : ' ';
      i = end + 2;
    } else if (/\s/.test(c)) {
      while (i < css.length && /\s/.test(css[i])) i++;
      if (result && !/[{};]$/.test(result) && !/[{};]/.test(css[i] || '')) result += ' ';
    } else { result += c; i++; }
  }
  return result.trim();
}

/** Resolve relative URLs against their source, then relocate to dist. */
function relocate(css, file) {
  return css.replace(/url\(\s*(?:"((?:\\.|[^"\\])*)"|'((?:\\.|[^'\\])*)'|([^\s)]+))\s*\)/gi,
    (match, double, single, bare) => {
      const value = double ?? single ?? bare;
      if (/^(?:[a-z][a-z\d+.-]*:|\/|#)/i.test(value)) return match;
      const split = value.search(/[?#]/);
      const pathname = split < 0 ? value : value.slice(0, split);
      const suffix = split < 0 ? '' : value.slice(split);
      const absolute = path.resolve(themeRoot, path.dirname(file), pathname);
      if (!absolute.startsWith(themeRoot + path.sep) || !fs.existsSync(absolute)) {
        throw new Error(`Missing/unsafe CSS URL in ${file}: ${value}`);
      }
      return `url("${path.relative(out, absolute).split(path.sep).join('/')}${suffix}")`;
    });
}

const manifest = {};
const outputs = new Map();
function bundle(name, sources, extra = '') {
  const css = sources.map(file => {
    const source = read(file);
    if (/@import\b/i.test(source)) throw new Error(`Unexpected nested import: ${file}`);
    return relocate(source, file);
  }).join('\n') + '\n' + extra;
  const content = compact(css) + '\n';
  const version = crypto.createHash('sha256').update(content).digest('hex').slice(0, 16);
  const file = `${name}.min.css`;
  outputs.set(file, content);
  manifest[name] = { file: `/assets/css/dist/${file}`, version, sources, bytes: Buffer.byteLength(content) };
}
// This shared prefix precedes the first page-specific source in every view.
bundle('core', ['assets/css/legacy-compat.css', 'assets/css/utilities.css', 'assets/css/modules/variables.css', 'assets/css/modules/header.css']);
for (const [name, feature] of Object.entries(profiles)) {
  // Imported sources retain their original order. Re-enqueued sources keep their
  // final effective occurrence, eliminating duplicates without reordering rules.
  const selected = new Set([...shared, ...feature]);
  const late = feature.filter(file => !['hero.css', 'daily-narrative.css', 'news-monitoring.css'].includes(file));
  const early = imports.filter(file => selected.has(file) && !late.includes(file) && !['variables.css', 'header.css'].includes(file));
  const featureBeforeAccount = late.filter(file => !experience.includes(file) && file !== 'member-portal.css');
  const final = [...early, ...featureBeforeAccount, 'account-menu.css',
    ...late.filter(file => file === 'member-portal.css'), ...late.filter(file => experience.includes(file))];
  // Empty-state rules followed the imported index in the original stylesheet.
  const sources = final.map(file => `assets/css/modules/${file}`);
  bundle(name, sources, tail);
}
bundle('auth-dialog', ['assets/css/modules/auth-dialog.css']);
const php = '<?php\n// Generated by assets/css/build-css.mjs; do not edit.\nreturn ' +
  '[' + Object.entries(manifest).map(([name, entry]) =>
    `\n    '${name}' => ['file' => '${entry.file}', 'version' => '${entry.version}'],`).join('') + '\n];\n';
outputs.set('manifest.php', php);
outputs.set('manifest.json', JSON.stringify(manifest, null, 2) + '\n');
if (!check) fs.mkdirSync(out, { recursive: true });
for (const [file, content] of outputs) {
  const target = path.join(out, file);
  if (check) {
    if (!fs.existsSync(target) || fs.readFileSync(target, 'utf8') !== content) throw new Error(`Stale CSS build: ${file}`);
  } else if (!fs.existsSync(target) || fs.readFileSync(target, 'utf8') !== content) fs.writeFileSync(target, content);
}
console.log(`${check ? 'Verified' : 'Built'} ${Object.keys(manifest).length} CSS bundles; no dependencies or browser imports.`);
