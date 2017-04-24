<?php
/**
 * Plugin Name: WP Extra File Types
 * Description: Plugin to let you extend the list of allowed file types supported by the Wordpress Media Library.
 * Plugin URI: http://www.airaghi.net/en/2015/01/02/wordpress-custom-mime-types/
 * Version: 0.3.2
 * Author: Davide Airaghi
 * Author URI: http://www.airaghi.net
 * License: GPLv2 or later
 */
 
defined('ABSPATH') or die("No script kiddies please!");


class WPEFT {
	
	private $lang         = array();
	private $is_multisite = false;
	private $types_list   = false;
	
	public function __construct() {
		// language
		require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages.php' );
		$lang = get_bloginfo('language','raw');
		if (!isset($wpeft_lang[$lang])) {
			$lang = 'en-US';
		}
		$this->lang = $wpeft_lang[$lang];
		// mime types' list
		$main_list = dirname(__FILE__).DIRECTORY_SEPARATOR.'mime-list.txt';
		if (file_exists($main_list)) {
			$wpeft_list = trim(file_get_contents($main_list));
			if ($wpeft_list) {
				$this->types_list = @unserialize($wpeft_list);
			} else {
				$this->types_list = false;
			}
		}
		// multisite
		$this->is_multisite = is_multisite();
	}

	private function defaults() {
		return array (
			// text
			'txt' => 'text/plain',
			// compressed
			'7z'  => 'application/x-7z-compressed',
			'bz2' => 'application/x-bzip2',
			'gz'  => 'application/x-gzip',
			'tgz' => 'application/x-gzip',
			'txz' => 'application/x-xz',
			'xz'  => 'application/x-xz',
			'zip' => 'application/zip'
		);
	}
	
	public function settings() {
		register_setting('wp-extra-file-types-page','wpeft_types');	
		register_setting('wp-extra-file-types-page','wpeft_custom_types');
	}

	public function admin() {
		add_submenu_page( 
			'options-general.php',
			$this->lang['ADMIN_PAGE_TITLE'] , $this->lang['ADMIN_MENU_TITLE'], 
			'manage_options', 
			'wp-extra-file-types-page', 
			array($this,'admin_page')
		);
		add_action( 'admin_init', array($this,'settings') );
		if (get_option('wpeft_types','')=='') {
			update_option('wpeft_types',$this->defaults());
		}
		if (get_option('wpeft_custom_types','')=='') {
			update_option('wpeft_custom_types','');
		}
	}
	
	public function admin_page() {
		if (!current_user_can('manage_options')) { wp_die('Unauthorized'); }
		if (isset($_POST['do_save']) && $_POST['do_save']=='1') {
			// save !!!
			if (!isset($_POST['ext']) || !is_array($_POST['ext'])) {
					update_option('wpeft_types','none');
			} else {
				$info = array();
				foreach ($this->types_list as $t) {
					foreach ($t->extensions as $te) {
						$info[$te] = $t->mime_type;
					}
				}
				$array = array();
				foreach ($_POST['ext'] as $the_ext) {
					$array[ $the_ext ] = $info['.'.$the_ext];
				}
				$ok = update_option('wpeft_types',$array);
				if (!$ok) {
					$ok = add_option('wpeft_types',$array);
				}
			}
			if (isset($_POST['custom_d'])) {
				$custom = array();
				foreach ($_POST['custom_d'] as $k=>$description) {
					$description = trim($description);
					if ($description != '') {
						$ext  = trim($_POST['custom_e'][$k]);
						$mime = trim($_POST['custom_m'][$k]);
						if ($ext=='' || $mime=='')  { continue; }
						if (!substr($ext,0,1)=='.') { $ext = '.'.$ext; }
						$custom[] = array( 'description'=>$description, 'extension'=>$ext, 'mime'=>$mime );
					}
				}
				update_option('wpeft_custom_types',$custom);
			}
		}
		$selected = get_option('wpeft_types','');
		if (!$selected) {
			$selected = $this->defaults();
		}
		if (!is_array($selected)) {
			$selected = array();
		}
		$exts = array_keys($selected);
		
		$custom = get_option('wpeft_custom_types','');
		if (!$custom) {
			$custom = array();
		}		
		?>
		<div class="wrap">
		<h2><?php echo htmlentities($this->lang['ADMIN_PAGE_TITLE']);?></h2>
		<p><?php echo htmlentities($this->lang['TEXT_CHOOSE']);?></p>
		<form  method="post" action="options-general.php?page=wp-extra-file-types-page" name="wpeft_form" onsubmit="return checkExt()">
			<input type="hidden" name="do_save" value="1" />
			<?php settings_fields( 'wp-extra-file-types-page' ); ?>
			<?php do_settings_sections( 'wp-extra-file-types-page' ); ?>
			<table>
				<?php
				foreach ($this->types_list as $type) {
					foreach ($type->extensions as $ext) {
						$ext0 = str_replace('.','',$ext);
						if (''==$ext0) { continue; }
						?>
						<tr>
							<td valign="top"><?php echo $type->application;?></td>
							<td valign="top"><?php echo $ext;?></td>
							<td valign="top">
								<input type="checkbox" name="ext[]" value="<?php echo $ext0;?>" <?php if (in_array($ext0,$exts)) echo 'checked="checked"'; ?> >
							</td>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<script>
				var wpeft_ext_position = 0;
				function checkExt() {
					var f   = document.wpeft_form;
					var els = f.elements;
					var i   = 0;
					var m   = els.length;
					var el  = null;
					for (i=0;i<m;i++) {
						el = els[i];
						if (el.name.match(/^custom\_/) && el.value=='') {
							alert("<?php echo str_replace('"','',$this->lang['MSG_REQUIREDS']); ?>");
							return false;
						}
					}
					return true;
				}
				function addExt(a,b,c,force_remove) {
					++ wpeft_ext_position;
					var t = document.getElementById('wpeft_ext_table');
					if (!a) { a = ''; }
					if (!b) { b = ''; }
					if (!c) { c = ''; }
					var tr0, td0, td1, td2, td3, i0, i1, i2;
					tr0 = document.createElement('tr');
					tr0.setAttribute('id','wpeft_ext_'+wpeft_ext_position);
					td0 = document.createElement('td');    td1 = document.createElement('td');    td2 = document.createElement('td');
					i0  = document.createElement('input'); i1  = document.createElement('input'); i2  = document.createElement('input');
					i0.setAttribute('type','text');        i1.setAttribute('type','text');        i2.setAttribute('type','text');
					i0.setAttribute('name','custom_d[]');  i1.setAttribute('name','custom_e[]');  i2.setAttribute('name','custom_m[]');
					i0.value = a;                          i1.value = b;                          i2.value = c;
					td0.appendChild(i0); td1.appendChild(i1); td2.appendChild(i2);
					td3 = document.createElement('td'); 
					td3.innerHTML = '';
					td3.innerHTML = td3.innerHTML + '<input type="button" value="+" onclick="addExt(\'\',\'\',\'\',true)"> ';
					if (a || force_remove) {
						td3.innerHTML = td3.innerHTML + ' <input type="button" value="-" onclick="removeExt('+wpeft_ext_position+')"> ';
					}					
					tr0.appendChild(td0); tr0.appendChild(td1); tr0.appendChild(td2); tr0.appendChild(td3);
					t.appendChild(tr0);
				}				
				function removeExt(pos) {
					var x = document.getElementById('wpeft_ext_'+pos);
					x.parentNode.removeChild(x);
				}
			</script>
			<p><b><?php echo htmlentities($this->lang['ADD_EXTRAS']); ?></b> <input type="button" value="+" onclick="addExt('','','',true)" /></p>
			<table id="wpeft_ext_table" border="1">
				<tr>
					<td><?php echo htmlentities($this->lang['DESCRIPTION']); ?> (*)</td>
					<td><?php echo htmlentities($this->lang['EXTENSION']); ?> (*)</td>
					<td><?php echo htmlentities($this->lang['MIME_TYPE']); ?> (*)</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			(*) <?php echo htmlentities($this->lang['REQUIRED']); ?><br><br>
			<?php foreach ($custom as $element) { ?>
			<script>addExt("<?php echo str_replace('"','',$element['description']); ?>","<?php echo str_replace('"','',$element['extension']); ?>","<?php echo str_replace('"','',$element['mime']);?>");</script>
			<?php } ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}
	
	public function mime($mimes) {
		$opt = get_option('wpeft_types','');
		if (!$opt) {
			update_option('wpeft_types',$this->defaults());
			$opt = $this->defaults();
		}
		$optc = get_option('wpeft_custom_types','');
		if (!$optc) {
			$optc = array();
		}
		if (!is_array($opt) && is_string($opt)) {
				$opt = array();
		}
		if (!is_array($optc) && is_string($optc)) {
				$optc = array();
		} else {
			$_optc = array();
			foreach ($optc as $c) {
				if (substr($c['extension'],0,1)=='.') { $c['extension'] = substr($c['extension'],1); }
				$_optc[ $c['extension'] ] = $c['mime'];
			}
			$optc  = $_optc;
		}
		$ret =  array_merge($mimes,$opt,$optc);
		return $ret;
	}
	
}


$wpeft_obj = new \WPEFT();

add_action('admin_menu', array($wpeft_obj,'admin'));
add_filter('upload_mimes',array($wpeft_obj,'mime'));


/* add_filter('upload_mimes','add_extra_mime_types');

function add_extra_mime_types($mimes){
	return array_merge($mimes,array (
		// text
		'txt' => 'text/plain',
		// compressed
		'7z'  => 'application/x-7z-compressed',
		'bz2' => 'application/x-bzip2',
		'gz'  => 'application/x-gzip',
		'tgz' => 'application/x-gzip',
		'txz' => 'application/x-xz',
		'xz'  => 'application/x-xz',
		'zip' => 'application/zip'
	));
}
*/
	
?>