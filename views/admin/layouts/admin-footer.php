            </div>
        </main>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Admin JS -->
    <script src="<?= BASE_URL ?>js/admin.js"></script>
    <script src="<?= BASE_URL ?>js/product-form.js"></script>
    
    <script>
        // تهيئة Select2
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'اختر من القائمة',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    }
                }
            });
        });
    </script>
</body>
</html>