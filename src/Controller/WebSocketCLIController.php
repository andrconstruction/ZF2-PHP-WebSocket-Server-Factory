<?php
namespace WebSockets\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use WebSockets\Factory\ApplicationFactory;
use WebSockets\Exception;

/**
 * Class WebSocketCLIController
 *
 * @package     WebSockets\Controller
 * @since       PHP >=5.6
 * @version     v3.2.1
 * @author      Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright   Stanislav WEB
 * @license     Zend Framework GUI license (New BSD License)
 * @filesource  /vendor/stanislav-web/zf2-websocket-server-factory/src/Controller/WebSocketCLIController.php
 */
class WebsocketCLIController extends AbstractActionController {


	public function versionAction () {
	}

	/**
	 * systemAction() System command
	 * @access public
	 * @return console
	 */
	public function systemAction () {
		$request = $this->getRequest ();

		if ( !$request instanceof ConsoleRequest ) {
			throw new \RuntimeException( 'Use only for CLI!' );
		}

		// Try to start actions

		try {
			// Get system service name  from console and check if the user used --verbose or -v flag
			$option = $request->getParam ( 'option', false );
			$option = preg_replace ( '#"#', '', $option );
			if ( is_string ( $option ) ) {
				system ( $option, $val );
			}
		} catch ( Exception\ExceptionStrategy $e ) {
			echo $e->throwMessage ();
		}
	}

	/**
	 * openAction() Running socket - server
	 * @access public
	 * @return null
	 */
	public function openAction () {
		$request = $this->getRequest ();

		if ( !$request instanceof ConsoleRequest ) {
			throw new \RuntimeException( 'Use only for CLI!' );
		}

		// Try to start server

		try {

			// get factory container
			$factory = new ApplicationFactory( $this->getServiceLocator () );

			// applications from response <app>
			// get it @see /src/WebSockets/Application/Chat.php etc..

			$client = $request->getParam ( 'app' );

			$app = $factory->dispatch ( ucfirst ( $client ) );

			// bind events from application
			// ! must be implements of your every new Application
			$app->bind ( 'open', 'onOpen' );
			$app->bind ( 'message', 'onMessage' );
			$app->bind ( 'close', 'onClose' );

			// running server application
			$app->run ();
		} catch ( Exception\ExceptionStrategy $e ) {
			echo $e->getMessage ();
		}
	}

}
