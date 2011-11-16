<?php
$path = 'D:\work\vs_php\php-5.2.17\ext\yaf_svn';
$libname='yaf';
$libpath=getcwd();
$libpath.="/$libname";
if(!file_exists($libpath)){
	mkdir($libpath,777,TRUE);
}
function parse_yaf($path) {
	global $libpath;
	if (isset ( $path ) && is_string ( $path )) {
		$path = preg_replace ( '/\\\\/', '/', $path );
		$arr = glob ( "$path/*" );
		if (isset ( $arr ) && is_array ( $arr )) {
			foreach ( $arr as $k => &$v ) {
				if (is_dir ( $v )) {
					parse_yaf ( $v );
				}
				if (preg_match ( '/.*\.c$/', $v )) {
					if (file_exists ( $v )) {
						echo "$v\n";
						$matchs = array ();
						$class_name = '';
						$functions = array ();
						$index = 0;
						$file_content = file_get_contents ( $v );
						if (preg_match_all ( '/YAF_INIT_CLASS_ENTRY\\(ce,(.*?),.*\\)/', $file_content, $matchs )) {
							if (isset ( $matchs [1] [0] )) {
								echo $class_name = $matchs [1] [0], "\n";
							}
						}else{
						  continue;	
						}
						if (preg_match_all ( '/proto (.*) .*::(.*)/', $file_content, $matchs )) {
							if (isset ( $matchs [1] ) && is_array ( $matchs [1] )) {
								if (isset ( $matchs [2] ) && is_array ( $matchs [2] )) {
									$len = count ( $matchs [1] );
									for($i = 0; $i < $len; ++ $i) {
										++ $index;
										if (isset ( $matchs [1] [$i] ) && isset ( $matchs [2] [$i] )) {
											$matchs [2] [$i]=trim($matchs [2] [$i]);
											if (is_string ( $matchs [2] [$i] )) {
												$strlen = strlen ( $matchs [2] [$i] );
												if ($matchs [2] [$i] [$strlen - 1] != ')' && $matchs [2] [$i] [$strlen - 2] != ')') {
													$matchs [2] [$i] .= '()';
												}
											}
											$matchs [0] = str_replace ( 'proto ', '', $matchs [0] );
											$matchs [2] [$i]=str_replace('void', '', $matchs [2] [$i]);
											$matchs [2] [$i]=str_replace('[', '', $matchs [2] [$i]);
											$matchs [2] [$i]=str_replace(']', '', $matchs [2] [$i]);
											$matchs [2] [$i]=str_replace('E_ALL | E_STRICT', 'NULL', $matchs [2] [$i]);
											$matchs [2] [$i]=str_replace('*', '$', $matchs [2] [$i]);
											$matchs [2] [$i]=str_replace('$mixed', 'mixed', $matchs [2] [$i]);
											$matchs [1] [$i]=str_replace('parotected', 'protected', $matchs [1] [$i]);
											$matchs [1] [$i]=str_replace('staitc', 'static', $matchs [1] [$i]);
											$functions [$index] ['func'] = $matchs [1] [$i] . '  function ' . $matchs [2] [$i] . "{}\n";
											$functions [$index] ['comment'] = $matchs [0] [$i];
										}
									}
								}
							}
						}
						$matches = array ();
						$class_name = str_replace ( '"', '', $class_name );
						$class_temp = "<?php\n";
						$class_temp .= "/**\n *$class_name\n * @since 2.0\n*/\n\n";
						$class_temp .= "class $class_name{\n\n";
						
						foreach ( $functions as $k => $v2 ) {
							$params = array ();
							if (! isset ( $v2 ['func'] ))
								continue;
							if (preg_match ( '/\\((.*)\\)/', $v2 ['func'], $matches )) {
								if (isset ( $matches [1] )) {
									$tarr = explode ( ',', $matches [1] );
									if (is_array ( $tarr )) {
										foreach ( $tarr as $k => $v1 ) {
											$v1 = trim ( $v1 );
											$p = explode ( ' ', $v1 );
											$params [] = $p;
										}
									}
								}
							}
							$class_temp .= "/**\n * " . $v2 ['comment'] . "\n";
							if (! isset ( $params ))
								continue;
							foreach ( $params as $p ) {
								if (isset ( $p [0] ) && ! isset ( $p [1] )) {
									$class_temp .= " * @param $p[0]\n";
								} else if (isset ( $p [0] ) && isset ( $p [1] )) {
									$class_temp .= " * @param $p[0] $p[1]\n";
								} else if (isset ( $p [0] ) && isset ( $p [1] ) && isset ( $p [2] ) && isset ( $p [3] )) {
									$class_temp .= " * @param $p[0] $p[1] $p[2] $p[3]\n";
								}
							}
							$class_temp .= " * @return\n";
							$class_temp .= " * @since 2.0\n*/\n\n";
							$class_temp .= $v2 ['func'] . "\n";
						
						}
						$class_temp .= "}";
						print_r ( $class_temp );
						file_put_contents($libpath."/$class_name.php", $class_temp);
						echo "\n";
					
					}
				}
			}
		}
	}
}
parse_yaf ( $path );
