<?php

	add_action('admin_menu', 'bizpanda_debug_addon_menu');

	function bizpanda_debug_addon_menu()
	{
		add_submenu_page('edit.php?post_type=opanda-item', 'Отчеты и отладка', 'Отчеты и отладка', 'manage_options', 'bizpanda-debug', 'bizpanda_debug_addon_page');
	}

	function bizpanda_debug_addon_page()
	{
		if( !current_user_can('manage_options') ) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		if( isset($_POST['bizpanda_debug_start_reports']) ) {
			require_once(BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/includes/class.zip-archive.php');

			$reposts_dir = BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/reports';
			$reports_temp = $reposts_dir . '/temp';
			$factory_dir = OPANDA_BIZPANDA_DIR . '/libs';
			$factory_hash_reports = '';

			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($factory_dir)) as $filename) {
				// filter out "." and ".."
				if( $filename->isDir() ) {
					continue;
				}

				$ext = pathinfo($filename, PATHINFO_EXTENSION);

				if( $ext != 'php' ) {
					continue;
				}

				$file = fopen($filename, 'r');
				$content = fread($file, filesize($filename));
				fclose($file);

				$factory_hash_reports .= "hash: " . md5($content) . ": $filename\n";
			}

			$f = fopen($reports_temp . '/hash-reports.txt', 'w+');
			fputs($f, $factory_hash_reports);
			fclose($f);

			$base_info = 'Версия php: ' . phpversion() . "\n";
			$base_info .= 'Объем памяти: ' . memory_get_usage() . "\n";
			$base_info .= 'Версия Wordpress: ' . get_bloginfo('version') . "\n";
			$bizpanda_session = isset($_COOKIE['bp_ut_session'])
				? $_COOKIE['bp_ut_session']
				: null;
			$base_info .= 'Данные сессии: ' . $bizpanda_session . "\n";

			$base_info .= "=====================\n";

			$install_plugins_report = "Установленные плагины\n";
			$install_plugins_report .= "=====================\n";

			$plugins = get_plugins();

			foreach($plugins as $path => $plugin) {
				if( is_plugin_active($path) ) {
					$install_plugins_report .= $plugin['Name'] . "\n";
				}
			}

			$f2 = fopen($reports_temp . '/site-info.txt', 'w+');
			fputs($f2, $base_info . $install_plugins_report);
			fclose($f2);

			Bizpanda_ExtendedZip::zipTree(OPANDA_BIZPANDA_DIR . '/libs', BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/reports/temp/factory.zip', ZipArchive::CREATE);

			$download_file_name = 'bizpanda-report-' . date('Y.m.d-H.i.s') . '.zip';
			$download_file_path = BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/reports/' . $download_file_name;

			Bizpanda_ExtendedZip::zipTree(BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/reports/temp', $download_file_path, ZipArchive::CREATE);

			array_map('unlink', glob(BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . "/reports/temp/*"));

			if( file_exists($download_file_path) ) {
				echo '<br><br>Скачайте подготовленный нами отчет, чтобы отравить его в службу поддержки плагина.<br>';
				echo '<a href=" ' . BZDA_POPUPS_ADN_DEBUG_PLUGIN_URL . '/reports/' . $download_file_name . '">Скачать отчет</a>';
			}
		}

		?>
		<div class="wrap">
			На этой странице вы сможете сформировать отчеты об ошибках, для службы поддержки.
			<form method="post"><input type="submit" name="bizpanda_debug_start_reports" value="Сформировать отчет">
			</form>
		</div>
	<?php
	}

