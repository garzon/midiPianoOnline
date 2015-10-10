<?php
//813626073@qq.com

class Visitor {
	private static $cookieExpireTime = 604800;
	public static $user = null;

	public static function password_hash($pwd) {
		return password_hash($pwd, PASSWORD_BCRYPT);
	}

	public static function hash_password($password) {
		return self::password_hash($password);
	}

	public static function verify_password(User $user, $pwd) {
		return password_verify($pwd, $user->hash);
	}

	protected static function generate_token_text($password, $lastLoginTime) {
		$lastLoginTime = strval($lastLoginTime);
		return $password . SECRET_SALT . '_' . strval($lastLoginTime);
	}

	protected static function generate_token($password, $lastLoginTime) {
		return self::password_hash(self::generate_token_text($password, $lastLoginTime));
	}

	public static function verify_token(User $user, $token) {
		if (!$user->lastLoginTime) return false;
		if (abs($user->lastLoginTime - time()) > self::$cookieExpireTime) return false;
		return password_verify(self::generate_token_text($user->hash, $user->lastLoginTime), $token);
	}

	// 本函数被调用时该用户以前的所有token失效
	public static function generate_token_for_user(User $user) {
		$user->lastLoginTime = time();
		$user->save();
		return self::generate_token($user->hash, $user->lastLoginTime);
	}

	public static function setLoginCookie(User $user) {
		$newToken = self::generate_token_for_user($user);
		$expireTime = $user->lastLoginTime + self::$cookieExpireTime;
		setcookie('user_id', $user->id, $expireTime);
		setcookie('token', $newToken, $expireTime, "", "", false, true);
	}

	public static function logout() {
		setcookie('user_id', '');
		setcookie('token', '', null, "", "", false, true);
	}

	public static function login($key, $pwd) {
		$user = User::findOne(['name' => $key]);
		if (empty($user)) $user = User::findOne(['email' => $key]);
		if (!$user) return 'This user does not exist!';
		if (!self::verify_password($user, $pwd)) return 'The password does not match.';
		self::setLoginCookie($user);
		return 'success';
	}

	public static function register($name, $email, $pwd, $code = '') {
		if(strlen($name) < 3) return "The length of username is at least 4.";
		if(preg_match('/[^0-9A-Za-z]/', $name)) return 'Only alphabetical and numeric characters are allowed in the username.';

		$user = User::findOne(['name' => $name]);
		if($user) return 'This username is taken.';

		if(empty($user)) $user = User::findOne(['email' => $email]);
		if($user) return 'This email address is registered.';

		$user = new User();
		$user->isSuperman = false;
		$user->name = $name;
		$user->email = $email;
		$user->hash = self::hash_password($pwd);
		$user->save();
		self::setLoginCookie($user);
		return 'success';
	}

	/**
	 * @return User
	 */
	public static function user() {
		if (self::$user) return self::$user;
		$id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : '';
		$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '';
		if (empty($id) || empty($token)) return null;
		$user = User::fetch($id);
		if (!$user) return null;
		if (!self::verify_token($user, $token)) return null;
		self::$user = $user;
		return $user;
	}

	public static function checkLogin() {
		$visitor = self::user();
		if (!$visitor) {
			header("Location: " . DOMAIN . "/login.php");
			die('');
		}
	}

	public static function isSuperman() {
		$user = self::user();
		if (!$user) return false;
		return $user->isSuperman == true;
	}
}