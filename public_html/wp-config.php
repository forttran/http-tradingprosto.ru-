<?php
/**
	*Основные параметры WordPress.
	*
	* Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
	* секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
	* на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
	* wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
	*
	* Этот файл используется сценарием создания wp-config.php в процессе установки.
	* Необязательно использовать веб-интерфейс, можно скопировать этот файл
	* с именем "wp-config.php" и заполнить значения.
	*
	* @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wordpress');

/** Имя пользователя MySQL */
define('DB_USER', 'forttran');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'f1o2r3t4');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'c:^jcN_z`Bm]f]|_Mg|.q<-n$M&f}{Wcc||-*@>lg,hdXw,Dv.du<iQuBNw8Jjy[');
define('SECURE_AUTH_KEY',  'jSZDt(m&!R_L)io2AV00dwr~`v4@p|6,M}:qi#1A6|JH#~aNxk$l_O!=5wo@CY+e');
define('LOGGED_IN_KEY',    ';C9=z}dmQ5If8{lqwfXlKGr-o#2.#r3N/%3hjwXS$}WXVwCxWMI?F9,9hgGqUrJA');
define('NONCE_KEY',        'xsCNJ+5f6rKT]6~B>|[<Dkl)lVO,B^Jd+48r9c0]mm&q.MR/J?x*$kP1F(57h-{o');
define('AUTH_SALT',        'Ckm@ZZe+^bs;lbSU/[<,2cWS{Z3$J|zkGI6f/c,Y3tS5*Q/rVb/hg|x^HS.$yF1~');
define('SECURE_AUTH_SALT', '=s)GFx_/5aflihcWn%/IKt2x1`b~2VvgW5fkIQ.*l-@$vs>J9 <?Wy++o4c(v$ai');
define('LOGGED_IN_SALT',   'JKwM5kdni.g0Ek]fB5,)}~B6WD6-R-.98_H slI[fw{,L^qR)f%D!fI<q~Jb8a&E');
define('NONCE_SALT',       'kWyXFqaO]TZOL0s{ z/T2y?QK@`^c*J|w_;(m,l{7,=]$yn/yg|2}b^yg[oB29$R');

/**#@-*/

		/**
		 * Префикс таблиц в базе данных WordPress.
		 *
		 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
		 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
		 */
$table_prefix  = 'wp_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', true);
/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	ABSPATHdefine('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
if(is_admin()) {
 	add_filter('filesystem_method', create_function('$a', 'return "direct";' ));
	define( 'FS_CHMOD_DIR', 0751 );
}
