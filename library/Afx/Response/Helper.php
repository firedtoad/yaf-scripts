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
 * @package Afx_Response
 * @version $Id Helper.php
 * The Helper for response
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Response_Helper
{
     /**
      * json输出
      * @param array $arr
      * @param string $message
      * @param string $code
      */
    public static function makeResponse ($arr, $message = NULL, $code = '0000')
    {
        $res = array('data' => $arr, 'message' => $message,
        'code' => $code);
        ob_clean();
        exit(json_encode($res));
    }
    private static function _responseJSON ()
    {}
    private static function _responseXML ()
    {}
    private static function _responseHTML ()
    {}
    private static function _responseRaw ()
    {}
}
?>