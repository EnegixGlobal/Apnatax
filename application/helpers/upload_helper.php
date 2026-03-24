<?php
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('upload_file')) {
	function upload_file($name, $upload_path, $allowed_types, $file_name, $max_size = 10000, $replace = false)
	{
		$CI = get_instance();
		if (!$CI->load->is_loaded('upload')) {
			$CI->load->library('upload');
		}

		$upload_path = trim($upload_path);
		$relative_prefix = null;
		// Resolve ./paths to project root so uploads work when PHP CWD is not the web root (common on hosting).
		if (strncmp($upload_path, './', 2) === 0 && defined('FCPATH')) {
			$relative_prefix = trim(str_replace('\\', '/', substr($upload_path, 2)), '/');
			$upload_path = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relative_prefix);
		}

		$upload_path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $upload_path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if (!is_dir($upload_path)) {
			if (!@mkdir($upload_path, 0755, true) && !is_dir($upload_path)) {
				return array('status' => false, 'msg' => 'Upload folder could not be created. Check permissions on assets/ (775/755 and correct owner).');
			}
		}
		if (!is_writable($upload_path)) {
			return array('status' => false, 'msg' => 'Upload folder is not writable. Fix permissions on: ' . str_replace('\\', '/', $upload_path));
		}

		$file_name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_name)));
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = $allowed_types;
		$config['file_name'] = $file_name;
		$config['max_size'] = $max_size;
		if ($replace === true) {
			$config['overwrite'] = true;
		}
		$CI->upload->initialize($config);
		$return = array('status' => false, 'msg' => 'File Not Uploaded !!');

		if (!isset($_FILES[$name])) {
			return array(
				'status' => false,
				'msg' => 'No file received. If uploads work locally but not here, raise PHP post_max_size and upload_max_filesize on the server (post_max_size is ' . ini_get('post_max_size') . ').',
			);
		}

		$err = isset($_FILES[$name]['error']) ? (int) $_FILES[$name]['error'] : UPLOAD_ERR_NO_FILE;
		if ($err === UPLOAD_ERR_NO_FILE) {
			return array('status' => false, 'msg' => 'File Not Uploaded !!');
		}
		if ($err !== UPLOAD_ERR_OK) {
			$map = array(
				UPLOAD_ERR_INI_SIZE => 'File exceeds server upload_max_filesize.',
				UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE.',
				UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
				UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary folder for uploads.',
				UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk (check disk space and folder permissions).',
				UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
			);
			$return['msg'] = isset($map[$err]) ? $map[$err] : ('Upload error (code ' . $err . ').');
			return $return;
		}

		if (empty($_FILES[$name]['tmp_name']) || !is_uploaded_file($_FILES[$name]['tmp_name'])) {
			return array('status' => false, 'msg' => 'Invalid upload (temporary file missing).');
		}

		if (!$CI->upload->do_upload($name)) {
			$return['status'] = false;
			$return['msg'] = $CI->upload->display_errors('', '');
		} else {
			$filedata = $CI->upload->data();
			$file = $filedata['raw_name'] . $filedata['file_ext'];
			$return['status'] = true;
			if ($relative_prefix !== null) {
				$return['path'] = $relative_prefix . '/' . $file;
			} else {
				$src = $upload_path . $file;
				$return['path'] = ltrim(substr(str_replace('\\', '/', $src), 1), '/');
			}
		}

		return $return;
	}
}
