<?php
/**
 * AFX FRAMEWORK
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * @copyright Copyright (c) 2012 BVISON INC.  (http://www.bvison.com)
 */
/**
 * @package Afx_Db
 * @version $Id Adapter.php
 * The Pdo Class Adapter Provider Communication With The RelationShip Database
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Debug_Helper
{

    public static function print_r ()
    {
        // static $colors=array('red','yellow','green');
        $color = dechex(rand(100, 200)) . dechex(rand(100, 200)) . dechex(rand(100, 200));
        if (strlen($color) != 6)
        {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v)
        {
            if (is_object($v))
            {
                print_r($v);
                $ref = new ReflectionClass($v);
                $methods = $ref->getMethods();
                $consts = $ref->getConstants();
                $prosps = $ref->getProperties();
                $statics = $ref->getStaticProperties();
                $name = $ref->getName();
                echo 'name=' . $name . "\n";
                echo 'consts={';
                if ($consts && is_array($consts))
                {
                    foreach ($consts as $k2 => $v2)
                    {
                        if (method_exists($v2, '__toString'))
                        {
                            echo $v2->__toString();
                        }
                    }
                }
                echo "}\n";
                echo "Prosp={\n";
                if ($prosps && is_array($prosps))
                {
                    foreach ($prosps as $k3 => $v3)
                    {
                        if (method_exists($v3, '__toString'))
                        {
                            echo $v3->__toString();
                        }
                    }
                }
                echo "}\n";
                echo "static prosp={\n";
                if ($statics && is_array($statics))
                {
                    foreach ($statics as $k4 => $v4)
                    {
                        if (method_exists($v4, '__toString'))
                        {
                            echo $v4->__toString();
                        }
                    }
                }
                echo "}\n";
                echo "methods={\n";
                if ($methods && is_array($methods))
                {
                    foreach ($methods as $k1 => $v1)
                    {
                        if (method_exists($v1, '__toString'))
                        {
                            echo $v1->__toString();
                        }
                    }
                }
                echo "}\n";
            } else 
                if (is_array($v))
                {
                    print_r($v);
                } else 
                    if (is_string($v))
                    {
                        print_r($v);
                    }
        }
        echo '</pre>';
    }

    public static function var_dump ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255));
        if (strlen($color) != 6)
        {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v)
        {
            var_dump($v);
        }
        echo '</pre>';
    }

    public static function export ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255));
        
        if (strlen($color) != 6)
        {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v)
        {
            ReflectionClass::export($v);
        }
        echo '</pre>';
    }

    public function exportExtension ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255));
        if (strlen($color) != 6)
        {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v)
        {
            ReflectionExtension::export($v);
        }
        echo '</pre>';
    }
}
?>