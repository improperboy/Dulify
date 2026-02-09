        </div> <!-- End of admin-content -->
    </div> <!-- End of admin-container -->
    
    <!-- jQuery and Bootstrap JS for modals and other functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    
    <script src="js/admin.js"></script>
    <?php if (isset($additional_js)): ?>
    <script>
        <?php echo $additional_js; ?>
    </script>
    <?php endif; ?>
    
    <script>
    // Initialize tooltips and popovers
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        
        // Mobile sidebar toggle
        $('#sidebar-toggle').click(function() {
            $('.admin-sidebar').toggleClass('open');
            $('#sidebar-overlay').toggleClass('active');
            $(this).toggleClass('open');
        });
        
        $('#sidebar-overlay').click(function() {
            $('.admin-sidebar').removeClass('open');
            $('#sidebar-overlay').removeClass('active');
            $('#sidebar-toggle').removeClass('open');
        });
    });
    </script>
</body>
</html>
