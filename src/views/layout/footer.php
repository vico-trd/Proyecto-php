</main> <footer class="bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Tienda de Ropa - Todos los derechos reservados</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
</body>
</html>