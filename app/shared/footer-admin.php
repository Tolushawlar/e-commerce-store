        </div>
        </main>

        <!-- Core Scripts -->
        <script src="/assets/js/core/api.js"></script>
        <script src="/assets/js/core/auth.js"></script>
        <script src="/assets/js/utils/helpers.js"></script>
        <script src="/assets/js/utils/components.js"></script>
        <script src="/assets/js/utils/activity-monitor.js"></script>

        <!-- Auth Check -->
        <script>
            // Require admin authentication
            auth.requireAdmin();

            // Display user name
            if (auth.user) {
                document.getElementById('userName').textContent = auth.user.name || 'Admin';
            }

            // Start activity monitoring
            if (auth.isAuthenticated()) {
                activityMonitor.start();
            }
        </script>
        </body>

        </html>