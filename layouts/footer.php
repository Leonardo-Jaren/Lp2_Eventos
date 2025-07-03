    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2025 Sistema LP2 Eventos. Todos los derechos reservados.</p>
        </div>
    </footer>
    
    <!-- Scripts globales -->
    <script>
        // Función para mostrar notificaciones toast
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-blue-500';
            
            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-70">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
        
        // Función para confirmar acciones
        function confirmarAccion(mensaje, callback) {
            if (confirm(mensaje)) {
                callback();
            }
        }
    </script>
</body>
</html>
