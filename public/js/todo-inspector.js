;(function() {
    'use strict';

    const TodoInspector = {
        elements: {},

        init() {
            this.cacheElements();
            this.initTheme();
            this.initLanguage();
            this.initSelectAll();
            this.initStatusSelects();
            this.fixSelectColors();
        },

        cacheElements() {
            this.elements = {
                html: document.documentElement,
                themeIcon: document.getElementById('theme-icon'),
                langSelect: document.getElementById('lang-select'),
                selectAll: document.getElementById('select-all'),
                taskCheckboxes: document.querySelectorAll('.task-checkbox'),
                statusSelects: document.querySelectorAll('.status-select')
            };
        },

        initTheme() {
            const savedTheme = localStorage.getItem('todo-inspector-theme');
            const configTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const theme = savedTheme || configTheme;

            this.setTheme(theme === 'dark');
        },

        setTheme(isDark) {
            const html = this.elements.html;
            const themeIcon = this.elements.themeIcon;

            if (isDark) {
                html.classList.add('dark');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
                localStorage.setItem('todo-inspector-theme', 'dark');
                document.cookie = "theme=dark; path=/; max-age=31536000";
            } else {
                html.classList.remove('dark');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
                localStorage.setItem('todo-inspector-theme', 'light');
                document.cookie = "theme=light; path=/; max-age=31536000";
            }

            this.fixSelectColors();
        },

        toggleTheme() {
            const isDark = this.elements.html.classList.contains('dark');
            this.setTheme(!isDark);
        },

        fixSelectColors() {
            const isDark = this.elements.html.classList.contains('dark');

            document.querySelectorAll('select').forEach(select => {
                if (isDark) {
                    select.style.backgroundColor = '#1f2937';
                    select.style.color = '#f3f4f6';
                    select.style.borderColor = '#4b5563';
                } else {
                    select.style.backgroundColor = '#ffffff';
                    select.style.color = '#1f2937';
                    select.style.borderColor = '#e5e7eb';
                }
            });
        },

        initLanguage() {
            if (!this.elements.langSelect) return;

            const savedLang = localStorage.getItem('todo-inspector-lang');
            if (savedLang && !window.location.search.includes('lang')) {
                this.elements.langSelect.value = savedLang;
            }

            this.elements.langSelect.addEventListener('change', (e) => {
                const url = new URL(window.location.href);
                url.searchParams.set('lang', e.target.value);
                localStorage.setItem('todo-inspector-lang', e.target.value);
                window.location.href = url.toString();
            });
        },

        initSelectAll() {
            if (!this.elements.selectAll) return;

            this.elements.selectAll.addEventListener('change', (e) => {
                this.elements.taskCheckboxes.forEach(cb => cb.checked = e.target.checked);
            });

            this.elements.taskCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const allChecked = Array.from(this.elements.taskCheckboxes).every(cb => cb.checked);
                    this.elements.selectAll.checked = allChecked;
                });
            });
        },

        initStatusSelects() {
            this.elements.statusSelects.forEach(select => {
                select.addEventListener('change', () => {
                    select.closest('form').submit();
                });
            });
        },

        async bulkUpdate(status) {
            const selectedIds = Array.from(document.querySelectorAll('.task-checkbox:checked'))
                .map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert(window.tasksTranslations?.select_task || 'Please select at least one task');
                return;
            }

            const statusLabel = status === 'done'
                ? (window.tasksTranslations?.done || 'Done')
                : (window.tasksTranslations?.in_progress || 'In Progress');

            const confirmMsg = (window.tasksTranslations?.confirm_update || 'Mark :count task(s) as :status?')
                .replace(':count', selectedIds.length)
                .replace(':status', statusLabel);

            if (!confirm(confirmMsg)) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.bulkActionUrl || '/todo-inspector/bulk';
            form.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            selectedIds.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }
    };

    window.TodoInspector = TodoInspector;
    window.toggleTheme = () => TodoInspector.toggleTheme();
    window.bulkUpdate = (status) => TodoInspector.bulkUpdate(status);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => TodoInspector.init());
    } else {
        TodoInspector.init();
    }
})();