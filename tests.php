<?php
error_reporting (E_ALL);
ini_set('dispay_errors', true);
define('WP_USE_THEMES', false);
$base = dirname(dirname(__FILE__));
require($base.'/../../wp-load.php');
if(!current_user_can('manage_options')) {
  die("Permission denied");
}

if($_GET['t']){
  if ($_GET['t'] && method_exists('MurmsAggregatorTests',$_GET['t'])){
    $f = $_GET['t'];
    if($_GET['p']){
      $result = MurmsAggregatorTests::$f($_GET['p']);
    }else{
      $result = MurmsAggregatorTests::$f();
    }
    MurmsAggregatorTests::print($result,$f.'('.$p.')');
  }
}

class MurmsAggregatorTests{

  public static function showNodes(){
    $config =  array(
      'schema_file' => plugin_dir_path(__FILE__).'schemas/default.json',
      'field_map_file' => plugin_dir_path(__FILE__).'schemas/field_map.json',
    );

    $ag = new Murmurations_Aggregator_WP($config);

    $result = $ag->load_nodes();

    $out = array();

    foreach ($ag->nodes as $id => $node) {
      $out[$id] = $node->data;
    }

    return $out;

  }

  public static function updateNode(){

    $config =  array(
      'schema_file' => plugin_dir_path(__FILE__).'schemas/default.json',
      'field_map_file' => plugin_dir_path(__FILE__).'schemas/field_map.json',
    );

    $ag = new Murmurations_Aggregator_WP($config);

    $url = 'https://test-index.murmurations.network/v1/nodes';

    $options['api_key'] = 'test_api_key';

    $json = Murmurations_API::getIndexJson($url,array(),$options);

    $index = json_decode($json,true);

    $node = $index['data'][0];

    $profile_url = $node['profile_url'];

    $json = Murmurations_API::getNodeJson($profile_url,$options);

    $node = new Murmurations_Node($ag->schema,$ag->field_map,$ag->settings);

    $build_result = $node->buildFromJson($json);

    echo llog($node,"Node after building from JSON");

    $id = $node->save();

    echo llog($id,"ID after saving post");

    $profile_url = $node->data['profile_url'];

    $db_node = new Murmurations_Node($ag->schema,$ag->field_map,$ag->settings);

    $post = $db_node->getPostFromProfileUrl($profile_url);

    echo llog($post,"WP Post loaded");

    $db_node->buildFromWPPost($post);

    echo llog($db_node,"Node after build from WP post");

  }


  public static function getIndexJson(){

    Murmurations_API::$logging_handler = array('MurmsAggregatorTests','print');

    $url = 'https://test-index.murmurations.network/v1/nodes';

    $options['api_key'] = 'test_api_key';
    $options['api_basic_auth_user'] = 'user';
    $options['api_basic_auth_pass'] = 'pass';

    $query = array(
      //'country' => 'Germany',
      //'last_validated' => 1541779342
    );

    self::print($url,"URL");
    self::print($options,"Options");
    self::print($query,"Query");

    $json = Murmurations_API::getIndexJson($url,$query,$options);

    self::print($json,"Index result");

  }

  public static function getNodeJson($value = "node-identifier"){

    $url = 'https://node/path'.$value;

    $options['api_key'] = 'test_api_key';

    $json = Murmurations_API::getNodeJson($url,$options);

    return json_decode($json,true);

  }

  public static function keyedApiRequest(){
    $url = 'https://test-index.murmurations.network/v1/nodes';

    $user = 'api_key';
    $pass = null;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $pass);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

    $headers = array();
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers){
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2)
          return $len;

        $headers[strtolower(trim($header[0]))][] = trim($header[1]);

        return $len;
      }
    );
    $result = curl_exec($ch);
    return array($result,print_r($headers,true));

  }

  public static function basicAuthTest(){

    $url = 'https://test-index.murmurations.network/v1/nodes';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_USERPWD, "user:pass");

    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_FAILONERROR, true);

    $result = curl_exec($ch);

    if($result === false){
      return "No result returned from cURL request to index. cURL error: ".curl_error($ch);
    }else{
      return $result;
    }

  }

  public static function indexRequest($params = null){
    $url = 'https://test-index.murmurations.network/v1/nodes';

    $query = array();
    if(is_array($params)){
      foreach ($params as $key => $value) {
        $query[$key] = $value;
      }
    }else{
      $query = array(
        'test_param' => 'test_value'
      );
    }

    $fields_string = http_build_query($query);

    $ch = curl_init();

    $user = 'test_api_key';
    $pass = null;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $pass);

    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_FAILONERROR, true);

    $result = curl_exec($ch);

    if($result === false){
      echo "No result returned from cURL request to index. cURL error:".curl_error($ch);
    }

    echo $result;

    echo "<pre>".print_r(json_decode($result,true),true);

  }

  public static function print( $out, $name = null ) {
    echo '<pre>';
    echo $name ? $name . ': ' : '';
    echo ( is_array( $out ) || is_object( $out ) ) ? print_r( (array) $out, true ) : $out;
    echo '</pre>';
  }

}




?>
