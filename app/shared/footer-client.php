        </div>
        </main>

        <!-- Core Scripts -->
        <script src="/assets/js/core/api.js"></script>
        <script src="/assets/js/core/auth.js"></script>
        <script src="/assets/js/utils/helpers.js"></script>
        <script src="/assets/js/utils/components.js"></script>

        <!-- Auth Check -->
        <script>
            // Require client authentication
            auth.requireClient();

            // Display user info
            if (auth.user) {
                document.getElementById('userName').textContent = auth.user.name || 'Client';
                if (auth.user.subscription_plan) {
                    document.getElementById('userPlan').textContent = auth.user.subscription_plan.charAt(0).toUpperCase() + auth.user.subscription_plan.slice(1) + ' Plan';
                }
            }
        </script>
        </body>

        </html>