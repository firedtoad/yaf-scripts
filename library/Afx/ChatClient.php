<?php
/*
 * @version $Id: ChatClient.php 1616 2013-07-22 11:43:26Z zhangwenhao $
 */
/**
 * 聊天客户端
 * @author wenhao.zhang
 * @example
 * 在控制器中  每个方法具体参数见  http://wiki.joyway.com/index.php?title=%E8%81%8A%E5%A4%A9%E6%8E%A5%E5%8F%A3
 * $this->load->library('ChatClient');
      $user=array('u'=>'dietoad','league_id'=>12,'scene_id'=>14);
      $client=ChatClient::Instance();

      if($client->login($user))
      {
         echo 'login_ok';
      }
      if($client->create_team(array('teamid'=>1234)))
      {
         echo 'create_team_ok';
      }
      if($client->chatmap(array('m'=>1234)))
      {
         echo 'chatmap_ok';
      }
      if($client->getmapusers(array('scene_id'=>14)))
      {
         echo 'getmapusers';
      }
      if($client->getallmapusers())
      {
         print_r($client->get_return_array());
         echo 'getallmapusers';
      }
      if($client->getusers())
      {
         print_r($client->get_return_array());
         echo 'getusers';
      }
 */
class Afx_ChatClient
{

    /**
     * @var ChatClient
     */
    private static $instance;
//    const HOST = '192.168.17.81';
    public static $HOST = '127.0.0.1';
    public static $PORT = 8888;
    public static $TIMEOUT = 1;
    public static $USER = '0';
    public static $LEAGUE_ID = 65535;
    public static $SCENE_ID = 65535;
    public static $SYSTEMKEY = 'sss';
    public static $IFACTOR=33;
    public static $ADMINKEY = '管理员密钥';
    const HOST = '127.0.0.1';
    const PORT = 8888;
    const TIMEOUT = 1;
    const USER = '0';
    const LEAGUE_ID = 65535;
    const SCENE_ID = 65535;
    const SYSTEMKEY = 'sss';
    const IFACTOR=33;
    const ADMINKEY = '管理员密钥';
    public static $msgs = array();

    static $auto_login = true;

    static $debug = FALSE;

    //    static $debug = TRUE;
    static $login_state = 0;

    static $last_command;

    /**
     * @var resource
     */
    private static $sock = 0;

    static $arr = array();
    public static function setConfig($config)
    {
        self::$HOST=isset($config['host'])?$config['host']:self::HOST;
        self::$PORT=isset($config['port'])?$config['port']:self::PORT;
        self::$TIMEOUT=isset($config['timeout'])?$config['timeout']:self::TIMEOUT;
        self::$USER=isset($config['user'])?$config['user']:self::USER;
        self::$SCENE_ID=isset($config['scene_id'])?$config['scene_id']:self::SCENE_ID;
        self::$LEAGUE_ID=isset($config['league_id'])?$config['league_id']:self::LEAGUE_ID;
        self::$SYSTEMKEY=isset($config['systemkey'])?$config['systemkey']:self::SYSTEMKEY;
        self::$ADMINKEY=isset($config['adminkey'])?$config['adminkey']:self::ADMINKEY;
    }
    
    function EncOrDec ($str, $key)
    {
        $str = str_split($str);
        $len = count($str);
        $klen = strlen($key);
        for ($i = 0; $i < $len; $i ++)
        {
            $str[$i]^=$key[($i+self::IFACTOR)%$klen];
        }
        $str = implode($str);
        return $str;
    }

    private function __construct ()
    {
        $host = self::$HOST;
        $port = self::$PORT;
        $timeout = self::$TIMEOUT;
        $user = self::$USER;
        $scene_id = self::$SCENE_ID;
        $league_id = self::$LEAGUE_ID;
        $key = self::$SYSTEMKEY;
        $adminkey = self::$ADMINKEY;
        $sock = fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($sock == false)
        {
            exit($errstr);
        }
        // 非阻塞
        stream_set_blocking($sock, 0);
        //超时时间
        stream_set_timeout($sock, 100);
        self::$sock = $sock;
        $this->__policy_request();
        if (self::$auto_login)
        {
            $this->login(array(
                'adminkey'=>$adminkey,
                'u'=>$user,
                'rid'=>0,
                'scene_id'=>$scene_id,
                'league_id'=>$league_id,
                'key'=>md5($key . $user)
            ));
        }
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @return Afx_ChatClient
     */
    public static function Instance ()
    {
        if (! self::$instance instanceof Afx_ChatClient)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 登录包
     * user:array('u'=>'dietoad','league_id'=>12,'scene_id'=>14)
     * @param array $user
     * @return boolean
     */
    public function login ($user)
    {
        $login = $this->__chat_common('login', $user, 'm', 'login_ok');
        if ($login)
        {
            self::$login_state = 1;
        }
        return $login;
    }
    
    /**
     * 小喇叭
     * data:array('u'=>'dieotad','m'=>'msg');
     * @param array $data
     * @return boolean
     */
    public function speeker($data)
    {
        return $this->__chat_common('speeker', $data, 'm', 'speek_ok');
    }
    
    /**
     * 系统消息
     * data:array('type'=>'user|all|team|list','t'=>'t','u'=>'dietoad',teamid=>12,'list'=>array('user1','user2'),'m'=>'hi map')
     * 必须参数
     * user:u
     * team:teamid
     * list:list
     * @param array $chat
     * @return boolean
     */
    public function system ($data = array())
    {
        return $this->__chat_common('system', $data, 'm', 'system_ok');
    }
    
    //任務類型
    public function mission ($data = array())
    {
        return $this->__chat_common('mission', $data, 'm', 'mission_ok');
    }

    /**
     * 某个地图发言
     * chat:array('scene_id'=>14,'m'=>'hi map')
     * @param array $chat
     * @return boolean
     */
    public function chatmap ($chat = array())
    {
        return $this->__chat_common('chatmap', $chat, 'c', 'chatmap');
    }

    /**
     * 世界发言
     * chat:array('m'=>'hi world')
     * @param array $chat
     * @return boolean
     */
    public function chatworld ($chat = array())
    {
        return $this->__chat_common('chatworld', $chat, 'c', 'chatworld');
    }

    /**
     * 好友聊天
     * chat:array('u'=>'dietoad','m'=>'hi friend')
     * @param array $chat
     * @return boolean
     */
    public function chatfriend ($chat = array())
    {
        return $this->__chat_common('chatfriend', $chat, 'c', 'chatfriend');
    }

    /**
     * 私聊聊天
     * chat:array('u'=>'dietoad','m'=>'hi private friend')
     * @param array $chat
     * @return boolean
     */
    public function chatprivate ($chat = array())
    {
        return $this->__chat_common('chatprivate', $chat, 'c', 'chatprivate');
    }

    /**
     * 军团聊天
     * chat:array('m'=>'hi league')
     * @param array $chat
     * @return boolean
     */
    public function chatleague ($chat = array())
    {
        return $this->__chat_common('chatleague', $chat, 'c', 'chatleague');
    }

    /**
     * 队伍聊天
     * chat:array('m'=>'hi team')
     * @param array $chat
     * @return boolean
     */
    public function chat_team ($chat = array())
    {
        return $this->__chat_common('chatteam', $chat, 'c', 'chatteam');
    }

    /**
     * 离线消息
     * chat:array('m'=>'hi team',from='bihuge','u'=>'dietoad')
     * @param array $chat
     * @return boolean
     */
    public function chatoffline ($chat = array())
    {
        return $this->__chat_common('chatoffline', $chat, 'c', 'chatoffline_ok');
    }

    /**
     * 指定地图玩家列表
     * data:array('scene_id'=>14,)
     * @param array $data
     * @return boolean
     */
    public function getmapusers ($data = array())
    {
        return $this->__chat_common('getmapusers', $data, 'm', 'get_mapusers_ok');
    }

    /**
     * 所有地图玩家列表
     * @return boolean
     */
    public function getallmapusers ()
    {
        return $this->__chat_common('getallmapusers', array(), 'm', 'get_all_map_users_ok');
    }

    /**
     * 所有玩家列表
     * @return boolean
     */
    public function getusers ()
    {
        return $this->__chat_common('getusers', array(), 'm', 'getusers_ok');
    }

    /**
     * 在线人数
     * @return boolean
     */
    public function get_online_count ()
    {
        return $this->__chat_common('getonlinecount', array(), 'm', 'getonlinecount_ok');
    }

    /**
     * 查询在线状态
     * status:array('friendlist'=>array('username1','username2'))
     * @return boolean
     */
    public function get_online_status ($status = array())
    {
        return $this->__chat_common('friendstatus', $status, 'm', 'friendstatus_ok');
    }

    /**
     * 创建队伍
     * team:array('teamid'=>1234)
     * @param array $team
     * @return boolean
     */
    public function create_team ($team = array())
    {
        return $this->__chat_common('createteam', $team, 'm', 'create_team_ok');
    }

    /**
     * 加入队伍
     * tean:array('teamid'=>1234)
     * @param array $team
     * @return boolean
     */
    public function join_team ($team = array())
    {
        return $this->__chat_common('jointeam', $team, 'm', 'jointeam_ok');
    }

    /**
     * 解散队伍
     * tean:array('teamid'=>1234)
     * @param array $team
     * @return boolean
     */
    public function destroy_team ($team = array())
    {
        return $this->__chat_common('destroyteam', $team, 'm', 'team_destroyed');
    }

    /**
     * 踢出某个玩家
     * tean:array('teamid'=>1234,'u'=>dietoad)
     * @param array $team
     * @return boolean
     */
    public function team_kick ($team = array())
    {
        return $this->__chat_common('teamkick', $team, 'm', 'team_kick_ok');
    }

    /**
     * 退出队伍
     * tean:array('teamid'=>1234)
     * @param array $team
     * @return boolean
     */
    public function quit_team ($team = array())
    {
        return $this->__chat_common('quitteam', $team, 'm', 'quitteam_ok');
    }

    /**
     * 加入黑名单
     * user:array('u'=>'dietoad')
     * @param array $user
     * @return boolean
     */
    public function add_blacklist ($user = array())
    {
        return $this->__chat_common('addblacklist', $user, 'm', 'addblacklist_ok');
    }

    /**
     * 从黑名单中移除
     * user:array('u'=>'dietoad')
     * @param array $user
     * @return boolean
     */
    public function remove_blacklist ($user = array())
    {
        return $this->__chat_common('removeblacklist', $user, 'm', 'removeblacklist_ok');
    }

    /**
     * 踢人
     * user:array('u'=>'dietoad')
     * @param array $user
     * @return boolean
     */
    public function kick_user ($user = array())
    {
        return $this->__chat_common('kickuser', $user, 'm', 'kickuser_ok');
    }

    /**
     * 操作成功后 返回的字符串 json 格式
     * 务必在某个操作成功后调用
     * @return string
     */
    public function get_return_string ()
    {
        return self::$msgs[self::$last_command];
    }

    /**
     * 操作成功后 返回的数组
     * 务必在某个操作成功后调用
     * @return array
     */
    public function get_return_array ()
    {
        return json_decode(self::$msgs[self::$last_command], true);
    }

    /**
     * 请求策略文件
     */
    private function __policy_request ()
    {
        fwrite(self::$sock, '<policy-file-request/>');
        fflush(self::$sock);
        usleep(8000);
        $times = 10;
        $i = 0;
        while ($i ++ < $times)
        {
            $data = $this->__read();
            usleep(1000);
            if ($data) break;
        }
    }

    /**
     * 检查返回数据是否包含某个字段
     * @param string $key
     * @param string $value
     * @param int $try_times 非阻塞尝试次数
     */
    private function __check_return ($key, $value, $try_times = 50)
    {
        $i = 0;
        while ($i ++ < $try_times)
        {
            $data = $this->__read();
            if (! $data)
            {
                //等待操作完成
                usleep(200);
                continue;
            }
            
//            $data=$this->EncOrDec($data, self::SYSTEMKEY);
            $data=substr($data, 4);
            $ret = json_decode($data, TRUE);
            self::$msgs[$ret['c']] = $data;
            if (isset($ret[$key]) && $ret[$key] == $value)
            {
                return true;
            } else
            {
                return true;
            }
        }
        return false;
    }

    private function __read ()
    {
        //var_dump(self::$sock);
        return fread(self::$sock, 10240);
    }

    private function __write ($arr = array())
    {
        if (isset($arr['c']))
        {
            $data = MCommon::EncodeWithCN($arr);
//            $data=$this->EncOrDec($data, self::SYSTEMKEY);
            fwrite(self::$sock, $data);
            fflush(self::$sock);
            usleep(100);
        }
    }

    private function __chat_common ($command, $data, $k, $v)
    {
        //未登录 发送非登录命令
        if ($command != 'login' && self::$login_state == 0)
        {
            echo 'please invoke $this->login() first before use other command';
            //            throw new UnAuthException('please invoke $this->login() first before use other command', 501);
            return false;
        }
        $arr = array(
            'c'=>$command
        );
        self::$last_command = $command;
        $arr = array_merge($arr, $data);
        $this->__write($arr);
        if ($this->__check_return($k, $v))
        {
            return true;
        } else
        {
            self::$debug && print_r(self::$msgs[self::$last_command]);
        }
        return false;
    }

    function __destruct ()
    {
        if (is_resource(self::$sock))
        {
            fclose(self::$sock);
        }
    }
}

