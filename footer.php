    <!-- ===== BOOTSTRAP JS ===== -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- ===== SCRIPT DARK MODE ===== -->
    <script>
    (function() {
        const saved = localStorage.getItem('darkMode') || 'on';
        if (saved === 'on') {
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
        }
    })();
    </script>
</body>
</html>