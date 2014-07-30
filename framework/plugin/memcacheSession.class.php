<?php
/**
 * @desc memcache session 插件类
 * @author gkl
 * @since 20140724
 */
class mfwMemcacheSession extends mfwSession
{
	/**
	 * @desc mem对象
	 * @var Memcache
	 */
	protected $mem;
	
	/**
	 * @desc 老数据md5校验码
	 * @var string
	 */
	protected $track;
	
	/**
	 * @desc 生成 session id
	 * @since 20140724
	 */
	protected function __createSessionId()
	{
		$ip = mfwCommon::getIp();
		$random = uniqid(mt_rand());
		return md5( $ip . $random );
	}
	
	/**
	 * @desc 获取session的memcache键值
	 * @param string $session_id
	 * @return string
	 */
	protected function __getMemKey( $session_id )
	{
		$host = mfwCommon::getHost();
		return 'SESSION:' . $host . ':' . $session_id;
	}
	
	/**
	 * @desc 获取校验码
	 * @param array $session 会话数据
	 * @return string
	 * @since 20140728 gkl 
	 */
	protected function __getTrack( $session )
	{
		return md5( json_encode( $session ) );
	}
	
	/**
	 * @desc 根据session_id获取会话
	 * @since 20140724 gkl
	 */
	protected function __loadSession()
	{
		$mem_key = $this->__getMemKey( $this->session_id );
		$mem_value = $this->mem->get( $mem_key );
		if ( $mem_value !== false )
		{
			$GLOBALS['_SESSION'] = json_decode( $mem_value, true );
		}
		$this->track = $this->__getTrack( $GLOBALS['_SESSION'] );
	}
	
	/**
	 * @desc 删除会话内容
	 * @param string $session_id
	 * @since 20140728 gkl
	 */
	protected function __delSession( $session_id )
	{
		$mem_key = $this->__getMemKey( $session_id );
		return $this->mem->delete( $mem_key );
	}
	
	/**
	 * @desc 设置会话数据
	 * @param array $session 会话数据
	 * @since 20140728 gkl
	 */
	protected function __setSession( $session_id, $session )
	{
		$mem_key = $this->__getMemKey( $session_id );		
		$mem_value = json_encode( $session );
		$mem_expire = $this->timeout;
		$res = $this->mem->set( $mem_key, $mem_value, null, $mem_expire );		
		return $res;
	}
	
	/**
	 * @desc session_id存cookie
	 * @param string $session_id 会话id
	 * @param integer $expire 会话超时时间
	 * @since 20140724 gkl
	 */
	protected function __setSessionId2Cookie( $session_id, $expire )
	{
		$cookie_https = null;
		$cookie_secure = null;
		$cookie_domain = mfwCommon::getHost();
		if ( mfwCommon::isHTTPS() )
		{
			$cookie_https = true;
			$cookie_secure = true;
		}
		setcookie( $this->session_name, $session_id, $expire, '/', $cookie_domain, $cookie_secure, $cookie_https );
	}
	
	/**
	 * @desc 获取session_id
	 * @param boolean $flush 是否刷新
	 * @return string	 
	 * @since 20140724
	 */
	protected function __getSessionId( $flush = false )
	{
		$session_id = isset( $_COOKIE[$this->session_name] ) ? $_COOKIE[$this->session_name] : false;
		if ( $session_id === false || $flush === true )
		{
			$session_id = $this->__createSessionId();
			$this->__setSessionId2Cookie( $session_id, time()+$this->timeout );
		}
		return $session_id;
	}
	
	/**
	 * @desc 构造函数
	 * @param array $servers
	 * @param string $session_name
	 * @param integer $timeout
	 */
	public function __construct( $servers, $session_name, $timeout )
	{
		if ( !empty( $servers ) )
		{
			foreach ( $servers as $server )
			{
				mfwMemcache::addServer( $server['host'], $server['port'] );
			}
		}
		else
		{
			throw new mfwException( 'memcache会话服务器配置有误' );
		}
		$this->mem = mfwMemcache::init();
		$this->session_name = $session_name;
		$this->timeout = $timeout;
		$this->track = '';
	}
	
	/**
	 * @see mfwSession::start()
	 */
	public function start()
	{
		$GLOBALS['_SESSION'] = array();
		$now = time();
		$this->session_id = $this->__getSessionId();
		mfwCommon::registerShutDown( array(
			$this,
			'update',
			) );
		$this->__loadSession();
	}
	
	/**
	 * @see mfwSession::reset()
	 */
	public function reset()
	{
		$GLOBALS['_SESSION'] = array();
		$old_session_id = $this->__getSessionId();
		$this->__delSession( $old_session_id );
		$this->session_id = $this->__getSessionId( true );
	}
	
	/**
	 * @see mfwSession::update()
	 */
	public function update()
	{
		$now_track = $this->__getTrack( $GLOBALS['_SESSION'] );		
		$is_change = $now_track === $this->track ? false : true;
		if ( $is_change === true )
		{
			$this->__setSession( $this->session_id, $GLOBALS['_SESSION'] );
		}
	}
}