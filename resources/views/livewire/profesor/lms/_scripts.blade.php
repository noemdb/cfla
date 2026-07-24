@script
<script>
    Alpine.data('tocNavigation', () => ({
        activeSection: 0,
        observer: null,

        init() {
            this.$nextTick(() => {
                const sections = this.$el.querySelectorAll('[data-section-index]');
                if (!sections.length) return;

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.activeSection = parseInt(entry.target.dataset.sectionIndex);
                        }
                    });
                }, { rootMargin: '-80px 0px -60% 0px' });

                sections.forEach(el => this.observer.observe(el));
            });
        },

        scrollTo(index) {
            const el = this.$el.querySelector(`[data-section-index="${index}"]`);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.activeSection = index;
            }
        },

        destroy() {
            if (this.observer) {
                this.observer.disconnect();
            }
        }
    }));
	    Alpine.data('helpWizardData', () => ({
	        activeTab: 0,
	        sidebarOpen: true,
	        tabs: [
	            { label: 'Visión General', short: 'Gral', icon: '📋' },
	            { label: 'Herramientas IA', short: 'IA', icon: '🤖' },
	            { label: 'Estados', short: 'Est', icon: '📊' },
	            { label: 'Secciones', short: 'Sec', icon: '📐' },
	            { label: 'Consejos', short: 'Tips', icon: '💡' },
	        ],
	    }));
</script>
@endscript
