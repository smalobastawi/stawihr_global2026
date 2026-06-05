(function () {
  'use strict';

  var NAV = [
    {
      title: 'Getting Started',
      items: [
        { id: 'index', title: 'Welcome', href: 'index.html' },
        { id: 'getting-started', title: 'Quickstart', href: 'getting-started.html' },
        { id: 'navigation', title: 'Navigate the System', href: 'navigation.html' },
        { id: 'dashboard', title: 'Dashboard', href: 'dashboard.html' }
      ]
    },
    {
      title: 'Self Service (ESS)',
      items: [
        { id: 'ess/index', title: 'ESS Overview', href: 'ess/index.html' },
        { id: 'ess/leave', title: 'Leave & Approvals', href: 'ess/leave.html' },
        { id: 'ess/payroll', title: 'Payroll & Loans', href: 'ess/payroll.html' },
        { id: 'ess/more', title: 'Team, Performance & More', href: 'ess/more.html' }
      ]
    },
    {
      title: 'Sidebar Modules',
      items: [
        { id: 'admin/administration', title: 'Administration', href: 'admin/administration.html' },
        { id: 'admin/policy-documents', title: 'Policy Documents', href: 'admin/policy-documents.html' },
        { id: 'admin/employees', title: 'Employee Management', href: 'admin/employees.html' },
        { id: 'admin/vehicle', title: 'Vehicle Management', href: 'admin/vehicle-management.html' },
        { id: 'admin/leave', title: 'Leave Management', href: 'admin/leave-management.html' },
        { id: 'admin/attendance', title: 'Attendance', href: 'admin/attendance.html' },
        { id: 'admin/payroll', title: 'Payroll', href: 'admin/payroll.html' },
        { id: 'admin/disciplinary', title: 'Disciplinary', href: 'admin/disciplinary.html' },
        { id: 'admin/performance', title: 'Performance Management', href: 'admin/performance.html' },
        { id: 'admin/recruitment', title: 'Recruitment', href: 'admin/recruitment.html' },
        { id: 'admin/training', title: 'Training', href: 'admin/training.html' },
        { id: 'admin/awards', title: 'Awards', href: 'admin/awards.html' },
        { id: 'admin/notice-board', title: 'Notice Board', href: 'admin/notice-board.html' },
        { id: 'admin/analytics', title: 'Analytics', href: 'admin/analytics.html' },
        { id: 'admin/employee-feedback', title: 'Employee Feedback', href: 'admin/employee-feedback.html' },
        { id: 'admin/settings', title: 'Settings', href: 'admin/settings.html' }
      ]
    }
  ];

  var searchIndex = [];
  var searchActiveIndex = -1;

  function getBasePath() {
    var depth = parseInt(document.body.getAttribute('data-depth') || '0', 10);
    if (depth <= 0) return '';
    var path = '';
    for (var i = 0; i < depth; i++) path += '../';
    return path;
  }

  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function highlightMatch(text, query) {
    if (!query) return escapeHtml(text);
    var lower = text.toLowerCase();
    var q = query.toLowerCase();
    var idx = lower.indexOf(q);
    if (idx === -1) return escapeHtml(text);
    return escapeHtml(text.slice(0, idx)) +
      '<mark>' + escapeHtml(text.slice(idx, idx + query.length)) + '</mark>' +
      escapeHtml(text.slice(idx + query.length));
  }

  function renderSidebar() {
    var container = document.getElementById('guide-sidebar-nav');
    if (!container) return;

    var base = getBasePath();
    var currentPage = document.body.getAttribute('data-page') || '';
    var html = '';

    NAV.forEach(function (section) {
      html += '<div class="guide-sidebar-section">';
      html += '<div class="guide-sidebar-section-title">' + section.title + '</div>';
      section.items.forEach(function (item) {
        var active = item.id === currentPage ? ' active' : '';
        html += '<a class="guide-sidebar-link' + active + '" href="' + base + item.href + '">' + item.title + '</a>';
      });
      html += '</div>';
    });

    container.innerHTML = html;
  }

  function renderToc() {
    var toc = document.getElementById('guide-toc-list');
    var content = document.querySelector('.guide-content');
    if (!toc || !content) return;

    var headings = content.querySelectorAll('h2[id], h3[id]');
    if (!headings.length) {
      var tocAside = document.querySelector('.guide-toc');
      if (tocAside) tocAside.style.display = 'none';
      return;
    }

    var html = '';
    headings.forEach(function (heading) {
      var cls = heading.tagName === 'H3' ? ' toc-h3' : '';
      html += '<a class="toc-link' + cls + '" href="#' + heading.id + '">' + heading.textContent + '</a>';
    });
    toc.innerHTML = html;
  }

  function initSidebarToggle() {
    var toggle = document.getElementById('guide-sidebar-toggle');
    var sidebar = document.getElementById('guide-sidebar');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 991 && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && e.target !== toggle) {
          sidebar.classList.remove('open');
        }
      }
    });
  }

  function fixTopbarLinks() {
    var base = getBasePath();
    document.querySelectorAll('[data-guide-base]').forEach(function (el) {
      var path = el.getAttribute('data-guide-base');
      el.setAttribute('href', base + path);
    });
  }

  function injectSearchBox() {
    var topbar = document.querySelector('.guide-topbar');
    var nav = document.querySelector('.guide-topbar-nav');
    if (!topbar || !nav || document.getElementById('guide-search')) return;

    var search = document.createElement('div');
    search.className = 'guide-search';
    search.id = 'guide-search';
    search.innerHTML =
      '<input type="search" class="guide-search-input" id="guide-search-input" ' +
      'placeholder="Search user guide…" autocomplete="off" spellcheck="false" ' +
      'aria-label="Search user guide" aria-expanded="false" aria-controls="guide-search-results" role="combobox">' +
      '<div class="guide-search-results" id="guide-search-results" hidden role="listbox"></div>';

    topbar.insertBefore(search, nav);
  }

  function scoreEntry(entry, tokens) {
    var haystack = (
      entry.title + ' ' +
      entry.section + ' ' +
      entry.description + ' ' +
      entry.keywords
    ).toLowerCase();

    var score = 0;

    tokens.forEach(function (token) {
      if (!token) return;
      var title = entry.title.toLowerCase();
      if (title === token) score += 100;
      else if (title.indexOf(token) === 0) score += 60;
      else if (title.indexOf(token) !== -1) score += 40;

      if (entry.section.toLowerCase().indexOf(token) !== -1) score += 15;

      var words = entry.keywords.split(/\s+/);
      if (words.indexOf(token) !== -1) score += 25;
      else if (entry.keywords.indexOf(token) !== -1) score += 12;

      if (entry.description.toLowerCase().indexOf(token) !== -1) score += 8;
      if (haystack.indexOf(token) !== -1) score += 4;
    });

    return score;
  }

  function searchGuide(query) {
    var tokens = query.toLowerCase().trim().split(/\s+/).filter(Boolean);
    if (!tokens.length) return [];

    return searchIndex
      .map(function (entry) {
        return { entry: entry, score: scoreEntry(entry, tokens) };
      })
      .filter(function (result) {
        return result.score > 0;
      })
      .sort(function (a, b) {
        return b.score - a.score;
      })
      .slice(0, 8)
      .map(function (result) {
        return result.entry;
      });
  }

  function closeSearchResults() {
    var results = document.getElementById('guide-search-results');
    var input = document.getElementById('guide-search-input');
    if (!results || !input) return;

    results.hidden = true;
    results.innerHTML = '';
    input.setAttribute('aria-expanded', 'false');
    searchActiveIndex = -1;
  }

  function renderSearchResults(results, query) {
    var container = document.getElementById('guide-search-results');
    var input = document.getElementById('guide-search-input');
    if (!container || !input) return;

    var base = getBasePath();

    if (!results.length) {
      container.innerHTML = '<div class="guide-search-empty">No results for “' + escapeHtml(query) + '”</div>';
      container.hidden = false;
      input.setAttribute('aria-expanded', 'true');
      searchActiveIndex = -1;
      return;
    }

    var html = '';
    results.forEach(function (entry, index) {
      var href = base + entry.href;
      html += '<a class="guide-search-result" role="option" href="' + href + '" data-index="' + index + '">';
      html += '<p class="guide-search-result-title">' + highlightMatch(entry.title, query) + '</p>';
      html += '<p class="guide-search-result-meta">' + escapeHtml(entry.section) + '</p>';
      html += '<p class="guide-search-result-desc">' + highlightMatch(entry.description, query) + '</p>';
      html += '</a>';
    });

    container.innerHTML = html;
    container.hidden = false;
    input.setAttribute('aria-expanded', 'true');
    searchActiveIndex = -1;
  }

  function setActiveResult(index) {
    var items = document.querySelectorAll('.guide-search-result');
    if (!items.length) return;

    items.forEach(function (el) {
      el.classList.remove('is-active');
    });

    if (index < 0 || index >= items.length) {
      searchActiveIndex = -1;
      return;
    }

    searchActiveIndex = index;
    items[index].classList.add('is-active');
    items[index].scrollIntoView({ block: 'nearest' });
  }

  function initSearch() {
    injectSearchBox();

    var input = document.getElementById('guide-search-input');
    var container = document.getElementById('guide-search-results');
    if (!input || !container) return;

    fetch('/docs/user-guide/assets/data/search-index.json')
      .then(function (response) {
        if (!response.ok) throw new Error('Search index unavailable');
        return response.json();
      })
      .then(function (data) {
        searchIndex = data;
      })
      .catch(function () {
        searchIndex = [];
      });

    input.addEventListener('input', function () {
      var query = input.value.trim();
      if (query.length < 2) {
        closeSearchResults();
        return;
      }
      renderSearchResults(searchGuide(query), query);
    });

    input.addEventListener('keydown', function (e) {
      var items = document.querySelectorAll('.guide-search-result');
      if (!items.length || container.hidden) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setActiveResult(searchActiveIndex + 1 >= items.length ? 0 : searchActiveIndex + 1);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setActiveResult(searchActiveIndex - 1 < 0 ? items.length - 1 : searchActiveIndex - 1);
      } else if (e.key === 'Enter' && searchActiveIndex >= 0) {
        e.preventDefault();
        window.location.href = items[searchActiveIndex].href;
      } else if (e.key === 'Escape') {
        closeSearchResults();
        input.blur();
      }
    });

    input.addEventListener('focus', function () {
      var query = input.value.trim();
      if (query.length >= 2) {
        renderSearchResults(searchGuide(query), query);
      }
    });

    document.addEventListener('click', function (e) {
      var search = document.getElementById('guide-search');
      if (search && !search.contains(e.target)) {
        closeSearchResults();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    fixTopbarLinks();
    renderSidebar();
    renderToc();
    initSidebarToggle();
    initSearch();
  });
})();
