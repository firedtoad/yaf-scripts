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
 * @package Afx_Module_Partition
 * @version $Id Logic.php
 * The Module Class Impliment The Core ORM CRUD Operator
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Module_Partition_Logic implements Afx_Module_Partition_PartitionInterface
{

    //	 public static $_formula='#key=#key/100000';
    public static $_partition_size = 10;

    /**
     * @var Afx_Module_Partition_Logic
     */
    private static $_instance;

    /**
     * @param Afx_Module_Abstract $moudule
     */
    public function doPartition (Afx_Module_Abstract $moudule, $key = 'id')
    {
        $l_suffix = intval($moudule->$key / self::$_partition_size);
        $l_suffix = $l_suffix > 0 ? $l_suffix : '';
        return $moudule->_tablename . $l_suffix;
    }

    public static function Instance ($new = FALSE)
    {
        if ($new) return new self();
        if (! self::$_instance instanceof Afx_Module_Partition_Logic)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
?>