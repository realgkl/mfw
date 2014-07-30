<?php
/**
 * @desc mfw 会话 抽象类
 * @author gkl
 * @since 20140724
 */
abstract class mfwSession
{
	/**
	 * @desc 会话名称
	 * @var string
	 */
	protected $session_name;
	
	/**
	 * @desc 会话超时时间(秒)
	 * @var integer
	 */
	protected $timeout;
	
	/**
	 * @desc session_id
	 * @var string
	 */
	protected $session_id;
	
	/**
	 * @desc 会话开启
	 * @since 20140724
	 */
	abstract public function start();
	
	/**
	 * @desc 会话复位
	 * @since 20140724
	 */
	abstract public function reset();
	
	/**
	 * @desc 会话更新
	 * @since 20140728 gkl
	 */
	abstract  public function update();
}