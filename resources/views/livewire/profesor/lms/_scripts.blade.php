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

	    Alpine.data('infografiaWizard', () => ({
	        infografiaModalOpen: $wire.entangle('infografiaModalOpen'),
	        infografiaPreviewOpen: $wire.entangle('infografiaPreviewOpen'),
	        infografiaConfig: $wire.entangle('infografiaConfig'),
	        infografiaPreviewSvg: $wire.entangle('infografiaPreviewSvg'),
	        infografiaError: $wire.entangle('infografiaError'),
	        generatingInfografia: $wire.entangle('generatingInfografia'),
	        configTab: 'estructura',

	        closeInfografiaModal() {
	            $wire.call('closeInfografiaModal');
	        },
	        closeInfografiaPreview() {
	            $wire.call('closeInfografiaPreview');
	        },
	        capitalize(value) {
	            if (!value) return '';
	            return value.charAt(0).toUpperCase() + value.slice(1);
	        },
	        generarInfografia() {
	            $wire.call('generateInfografia');
	        },
	        insertInfografiaEnEditor() {
	            $wire.call('insertInfografiaEnEditor');
	        },
	        downloadInfografiaSvg() {
	            if (!this.infografiaPreviewSvg) return;
	            const blob = new Blob([this.infografiaPreviewSvg], { type: 'image/svg+xml' });
	            const url = URL.createObjectURL(blob);
	            const a = document.createElement('a');
	            a.href = url;
	            a.download = 'infografia.svg';
	            a.click();
	            URL.revokeObjectURL(url);
	        }
	    }));
</script>
@endscript
