# نظام إدارة الشحن - Shipping Management System

نظام متكامل لإدارة عمليات الشحن البحري يتضمن إدارة العملاء، الشحنات، الفواتير، والمستندات.

## المتطلبات

- PHP 7.4 أو أحدث
- MySQL 5.7 أو أحدث
- خادم ويب (Apache/Nginx)
- Composer (لإدارة التبعيات)

## التثبيت

1. استنساخ المستودع:
   ```bash
   git clone [repository-url] shipping_v2
   cd shipping_v2
   ```

2. تثبيت التبعيات:
   ```bash
   composer install
   ```

3. إنشاء ملف الإعدادات:
   ```bash
   cp .env.example .env
   ```

4. تكوين ملف `.env` مع بيانات الاتصال بقاعدة البيانات:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=shipping_v2
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

5. إنشاء قاعدة البيانات:
   ```sql
   CREATE DATABASE shipping_v2;
   ```

6. استيراد هيكل قاعدة البيانات:
   ```bash
   mysql -u your_db_username -p shipping_v2 < database/schema.sql
   ```

7. تعيين الصلاحيات:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## الهيكل التنظيمي للمشروع

```
shipping_v2/
├── assets/              # الملفات الثابتة (CSS, JS, الصور)
├── config/              # ملفات الإعدادات
├── controllers/         # المتحكمات
├── database/            # ملفات قاعدة البيانات
├── includes/            # الملفات المشتركة
├── models/              # النماذج
├── views/               # واجهات المستخدم
│   ├── dashboard/       # لوحة التحكم
│   ├── shipments/       # واجهات إدارة الشحنات
│   ├── companies/       # واجهات إدارة الشركات
│   ├── documents/       # واجهات إدارة المستندات
│   └── layout/          # قوالب التصميم
├── .env                # متغيرات البيئة
├── index.php           # نقطة الدخول للتطبيق
└── README.md           # ملف المساعدة
```

## المميزات

- إدارة كاملة للشحنات من الإنشاء حتى التسليم
- إنشاء الفواتير والعروض السعرية
- تتبع حالة الشحنات
- إدارة العملاء وبياناتهم
- إصدار مستندات الشحن (بوليصة الشحن، إيصال الاستلام، إلخ)
- تقارير وإحصائيات
- واجهة مستخدم سهلة وسلسة
- دعم كامل للغة العربية

## الترخيص

هذا المشروع مرخص تحت [MIT License](LICENSE).
