<?php
/**
 * @desc 登录业务逻辑
 * @author gkl
 * @since 20140724
 */
class loginAct
{
	/**
	 * @desc 登录名
	 * @var string
	 */
	protected $login_user;
	
	/**
	 * @desc 登录密码
	 * @var string
	 */
	protected $login_pass;
	
	/**
	 * @desc 用户id
	 * @var integer
	 */
	protected $admin_user_id;
	
	/**
	 * @desc 用户称谓
	 * @var string
	 */
	protected $admin_user_name;
	
	/**
	 * @desc 用户权限
	 * @var array
	 */
	protected $login_role_id;
	
	/**
	 * @desc token
	 * @var string
	*/
	protected $token;
	
	/**
	 * @desc 登录错误信息
	 * @var string|false
	 */
	protected $error;
	
	/**
	 * @desc 密码错误重试次数
	 * @var integer
	 */
	protected $try_num;
	
	/**
	 * @desc 冻结账户时间（秒）
	 * @var integer
	 */
	protected $frozen;
	
	/**
	 * @desc 初始化，从会话中获取登录信息
	 */
	protected function __init()
	{
		$this->admin_user_id = isset( $_SESSION['admin_user_id'] ) ? intval( $_SESSION['admin_user_id'] ) : -1;
		$this->admin_user_name = isset( $_SESSION['admin_user_name'] ) ? intval( $_SESSION['admin_user_name'] ) : '';
		$this->login_user = isset( $_SESSION['login_user'] ) ? intval( $_SESSION['login_user'] ) : '';
		$this->login_pass = isset( $_SESSION['login_pass'] ) ? intval( $_SESSION['login_pass'] ) : '';
		$this->login_role_id = isset( $_SESSION['login_role_id'] ) ? intval( $_SESSION['login_role_id'] ) : array();
		$this->token = isset( $_SESSION['token'] ) ? $_SESSION['token'] : '';
		$this->error = isset( $_SESSION['login_error'] ) ? $_SESSION['login_error'] : false;
		$this->try_num = 3;
		$this->frozen = 60;
	}
	
	/**
	 * @desc 构造函数
	 */
	public function __construct()
	{
		$this->login_user = '';
		$this->login_pass = '';
		$this->admin_user_id = -1;
		$this->admin_user_name = '';
		$this->login_role_id = array();
		$this->token = '';
		$this->error = false;
		$this->__init();
	}
	
	/**
	 * @desc 设置token校验码
	 * @param string $token
	 * @since 20140729
	 */
	public function setToken( $token )
	{
		if ( is_string( $token ) && trim( $token ) !== '' )
		{
			$this->token = trim( $token );
			$_SESSION['token'] = trim( $token );
		}
	}
	
	/**
	 * @desc 获取登录错误信息
	 * @since 20140729 gkl
	 */
	public function getLoginError()
	{
		$error = $this->error;
		unset( $_SESSION['login_error'] );
		return $error;
	}
	
	/**
	 * @desc 设置登录错误信息
	 * @param string $value 错误信息
	 * @since 20140729 gkl
	 */
	public function setLoginError( $value )
	{
		if ( is_string( $value ) && trim($value) !== '' )
		{
			$this->error = trim( $value );
			$_SESSION['login_error'] = trim( $value );
		}
	}
	
	/**
	 * @desc 是否登录
	 * @return boolean
	 */
	public function isLogin()
	{		
		return $this->admin_user_id > 0 ? true : false;
	}
	
	/**
	 * @desc 验证token校验码
	 * @param string $token 校验码
	 * @return boolean
	 */
	public function checkToken()
	{
		$token = isset( $_POST['token'] ) ? $_POST['token'] : '';
		return $token === $this->token ? true : false;
	}
	
	/**
	 * @desc 登录成功后设置SESSION
	 * @since 20140730 gkl
	 */
	public function setUserSession()
	{
		$_SESSION['admin_user_id'] = $this->admin_user_id;
		$_SESSION['admin_user_name'] = $this->admin_user_name;
		$_SESSION['login_user'] = $this->login_user;
		$_SESSION['login_pass'] = $this->login_pass;
		$_SESSION['login_role_id'] = $this->login_role_id;
	}
	
	/**
	 * @desc 登录逻辑<br/>
	 * 返回 1 登录成功<br/>
	 * 返回-1 用户名或密码不正确<br/>
	 * 返回-2 账户冻结<br/>
	 * 返回-3 登录失败
	 * @return integer
	 */
	public function login()
	{
		$user = isset( $_POST['user_name'] ) ? trim( $_POST['user_name'] ) : '';
		$pass = isset( $_POST['user_pass'] ) ? trim( $_POST['user_pass'] ) : '';
		$model = new loginModel();
		$user_res = $model->login( $user );
		if ( $user_res !== false )
		{
			// 1、判断是否冻结
			if ( $user_res['f_unfrozen_time'] >= date('Y-m-d H:i:s') )
			{
				return -2;
			}
			// 2、判断密码是否正确
			if ( $user_res['f_pass'] !== md5( $pass ) )
			{
				$res = $model->loginFalid( $user_res['f_id'], $this->try_num, $this->frozen );
				if ( $res === false )
				{
					return -3;
				}
				return -1;
			}
			$data = array(
				'f_id'							=> $user_res['f_id'],
				'f_try_num'					=> 0,
				'f_unfrozen_time'		=> '0000-00-00 00:00:00',
			);
			$res = $model->loginSucc( $data );
			if ( $res !== true )
			{
				return -3;
			}
			$this->admin_user_id = $user_res['f_id'];
			$this->admin_user_name = $user_res['f_name'];
			$this->login_user = $user;
			$this->login_pass = $user_res['f_pass'];
			$this->login_role_id = array();			
			return 1;
		}
		else
		{			
			return -1;
		}
	}	
}