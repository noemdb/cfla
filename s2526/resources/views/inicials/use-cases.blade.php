@extends('inicials.layouts.dashboard.app')

@section('stylesheet')
    <style>
        .use-case-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .use-case-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .diagram-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .mermaid {
            text-align: center;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('main')
    <div class="container-fluid">
        <!-- Header -->
        @include('inicials.partials.use-cases.header')

        <!-- Navigation Tabs -->
        @include('inicials.partials.use-cases.navigation')

        <!-- Content Area -->
        <div class="row">
            <div class="col-12">
                <div class="tab-content" id="useCasesTabContent">
                    <!-- Overview Tab -->
                    @include('inicials.partials.use-cases.overview', ['useCases' => $useCases])

                    <!-- Individual Use Case Tabs -->
                    @include('inicials.partials.use-cases.authentication')
                    @include('inicials.partials.use-cases.weekly-planning')
                    @include('inicials.partials.use-cases.classroom-projects')
                    @include('inicials.partials.use-cases.evaluations')
                    @include('inicials.partials.use-cases.pedagogical-reports')
                    @include('inicials.partials.use-cases.special-reports')
                    @include('inicials.partials.use-cases.export-print')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        // Initialize Mermaid
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true
            }
        });

        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle tab clicks
            const tabLinks = document.querySelectorAll('[data-toggle="pill"]');
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');

                    // Hide all tab panes
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Remove active class from all nav links
                    document.querySelectorAll('.nav-link').forEach(navLink => {
                        navLink.classList.remove('active');
                    });

                    // Show target tab pane
                    const targetPane = document.querySelector(targetId);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active', 'fade-in');
                        this.classList.add('active');

                        // Re-render mermaid diagrams in the active tab
                        setTimeout(() => {
                            mermaid.init(undefined, targetPane.querySelectorAll('.mermaid'));
                        }, 100);
                    }
                });
            });

            // Handle use case card clicks
            document.querySelectorAll('.use-case-card').forEach(card => {
                card.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-target');
                    if (targetTab) {
                        const tabLink = document.querySelector(`[href="${targetTab}"]`);
                        if (tabLink) {
                            tabLink.click();
                        }
                    }
                });
            });
        });
    </script>
@endsection
