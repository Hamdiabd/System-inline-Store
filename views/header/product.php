<style>
            .quick-actions {
                
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
                width: 256px;
                height: 59px;
            background: white;
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--dark);
        }

        .action-btn:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: block;
        }
</style>

<body>
                 <div class="quick-actions">
                    <a href="#" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        إضافة منتج
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-file-invoice"></i>
                        إنشاء فاتورة
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-chart-line"></i>
                        تقرير المبيعات
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-cog"></i>
                        إعدادات المتجر
                    </a>
                </div>
</body>