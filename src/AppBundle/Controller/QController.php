<?php
// src/AppBundle/Controller/CarLogController.php

namespace AppBundle\Controller;
 
use AppBundle\Entity\User;
use AppBundle\Utils\Ses;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * CarLog controller.
 */
class QController extends Controller{
	
	/** @var User */
	protected $u = null;
	/**
	 * @return User
	 */
	protected function u(){
		//if ($this->u==null) $this->getUserCurr(null);
		//return $this-u;
		return $this->getUserCurr(null);
	}	
	
	protected $feImages =  array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp');
	
	private $em = null;
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function em(){
		//if ($this->em==null) $this->em = $this->getDoctrine()->getManager();
		$this->em = $this->getDoctrine()->getManager();
		return $this->em;
	}
	
	/**
	 * @param string $t
	 * @return \Doctrine\ORM\EntityRepository
	 */
	protected function r($t='User'){
		return $this->em()->getRepository('AppBundle:'.$t);
	}
	
	
	/**
	 * @param string $n name of the parameter
	 * @return string
	 */
	protected function p($n='',$v=null, $r=null){
		try {
			if (! is_null ( $r )) {//$this->container->get('request_stack')->getCurrentRequest();
				if (! is_null ( $v )) {
					$s = $r->getSession ();
					$s->set ( $n, $v );
				} else {
					$s = $r->getSession ();
					$ret = $s->get ( $n );
					if (! is_null ( $ret )) return $ret;
				}
			}
			return $this->getParameter($n);
		} catch (Exception $e) {return '';}
	}
	
	
    /**
     * @param - $username if null then current user, if not then search by username
     * @return unknown
     */
    protected function getUserCurr($username = null) {
        if (is_null($username)){
    	   if ($this->u != null) return $this->u;
    
            if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){  
                    //|| $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
                $this->u = $this->get('security.token_storage')->getToken()->getUser();
            }
    	   
    		/*if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
    			$this->u = null;
    		}else{
    			$this->u = $this->getUser();//get('security.token_storage')->getToken()->getUser();
    		}*/
    		return $this->u;
        }

    	//$em = $this->getDoctrine ()->getManager ();
    	$this->u = $this->r()->getUserByUsername($username);
    
    	if (!$this->u) {
    		throw $this->createNotFoundException ( 'Unable to find User by username: '.$username );
    	}
    
    	return $this->u;
    }
    
   
    
    /**
     *	upFotoAction1
     *
     * @param
     *
     */
    public function upFotoAction1(&$ret1) {
    	//*************RIGHTS************************************
//     	if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
//     		return $ret1 = new JsonResponse(
//     				array ("jcode" => 400,"jerr" => 'Sie sind nicht angemeldet',	"html" => '','fnid' => 0),
//     				200,
//     				array('Content-Type'=>'application/json')
//     		);
//     	}
    	//*************RIGHTS************************************
    
    	//$this->getUserCurr ( null );
    	//code in child class function
    	return true;
    }
    
    /**
     *	upFotoAction2
     *
     * @param
     * TODO - save only one size!
     *
     */
    public function upFotoAction2($id = null, $ix, $ffoto, $typ, $img_min_w=null,$img_min_h=null,$img_p_sm=null,$img_p_big=null,$un='') { 
    	
    	//code in child class function first
    	
    	$fncl='';
    	$fnor='';
    	$content = '';
    	/* @var UploadedFile */
    	if ($ffoto != null){
    		$fncl	= $ffoto->getClientOriginalName();
    		$jerr = "ok";
    		//FIXME - check max-size, if too big, than resize it
    		if ($ffoto->getSize()>0){
    			if(!in_array($ffoto->getMimeType(),$this->feImages) ) {
    				$jerr = "File ist not an image, type is: ".$ffoto->getMimeType();
    			}else{
    				//$fn_uid = "".($typ=='user'?$typsub:$typ);//Ses::uid(8);
    				//$fn_pref 	= $fn_uid.'-'.$this->u()->getId().'-'.$id.'-'.$ix;
    				//$fn_uid = "".Ses::uid(10);
    				//$fn_pref 	= "".($typ=='user'?$typsub:$typ).'-'.$fn_uid.'-'.$ix;
    				if ($typ=='admin_avatar')$fn_pref='avatar';
    				//elseif ($typ=='comyblog')$typ='ComyBlog';
    				$fnor 		= 'tmp_'.$fn_pref.'.'.$ffoto->guessExtension();
    				//if dimensions-settings are not defined, than use standard for all 
    				$img_p_sm = $img_p_sm==null ? array('constraint' => array('width' => 100, 'height' => 100)) : $img_p_sm;
    				$img_p_big = $img_p_big==null ? array(	'constraint' => array('width' => 200, 'height' => 200)) : $img_p_big;
    				$img_min_w = $img_min_w==null ? 100 : $img_min_w;
    				$img_min_h = $img_min_h==null ? 100 : $img_min_h;
    				$ffoto->move(Ses::getUpDirTmp($un), $fnor);
    				//TODO Create a scheduled tasks to remove not saved images
    				// check if image-path is not saved in DB, if user is offline or onliny more than 24h -> remove the image
    				list($ioW, $ioH) = getimagesize(Ses::getUpDirTmp($un)."/".$fnor);
    				if ($ioW<$img_min_w){
    					$jerr = "Minimum-Bild-Breite ist kleiner als ".$img_min_w."px";
    				}else if ($ioH<$img_min_h){
    					$jerr = "Minimum-Bild-H�he ist kleiner als ".$img_min_h."px";
    					
    				}
    				if ($ioH>=$img_min_h && $ioW>=$img_min_w){
//     					if (!Ses::imgResize(Ses::getUpDirTmp($un)."/".$fnor, Ses::getUpDirTmp($un)."/".$fnor, $img_p_sm)){
//     						$jerr = "Die Bildgrosse ist nicht angepasst zu ".$img_p_sm['constraint']['width']."x".$img_p_sm['constraint']['height'];
//     					};
    					if ($typ=='admin_avatar')$vw='admin/avatar';
    					//elseif ($typ=='comyblog')$typ='ComyBlog';
    					$content = $this->
    					renderView ( ''.$vw.'.html.twig', array (
    							'fnor' => $fnor,
    							$typ.'id' => $id,
    							'id' => $ix,
    							'un' => $un
    					) );
    				}
    			}
    	
    			$rjson = array (
    					"jcode" => 200,
    					"jerr" => $jerr,
    					'fncl' => $fncl,
    					'fnor' => $fnor,
    					$typ.'id' => $id,
    					'fnid' => $ix,
    					"html" => $content
    			);
    		 }else{
    			$rjson = array (
    					"jcode" => 400,
    					"jerr" => "Maximal-Bildgrosse ist grosser als: ".($ffoto->getMaxFilesize()/1024/1024)." MB",
    					'fncl' => $fncl,
    					"html" => $content,
    					$typ.'id' => $id,
    					'fnid' => $ix,
    			);
    		} 
    	}else{
    		$rjson = array (
    				"jcode" => 400,
    				"jerr" => "File-image is null",
    				'fncl' => "NoFile",
    				"html" => $content,
    				$typ.'id' => $id,
    				'fnid' => $ix,
    		);
    	}
    	
    	return $rjson;
    }
	
	/**
	 * upFotoAction3
	 *
	 * @param        	
	 *
	 */
	public function upFotoAction3($request, $typ) {
		//TODO Move this code to entity with Pre/Pos-update
		$ap = $request->request->get(''.$typ);
		if (is_null($ap))return true;
		if (key_exists ( "fotos", $ap )) {
			foreach ( $ap ['fotos'] as $p ) {
				// crop & resize the foto image
				if ($p ['foto_fnbig'] != null && $p ['foto_fnbig'] != '') {
					$img_p_sm = array (
							'constraint' => array (
									'width' => 120,
									'height' => 68 
							) 
					);
					// $logger->error('upload...'.print_r($p['foto_y'],1));
					Ses::imgCrop ( Ses::getUpDirTmp ($this->u()->getUsername()) . "/" . $p ['foto_fnbig'], $p ['foto_x'], $p ['foto_y'], $p ['foto_w'], $p ['foto_h'] );
					Ses::imgResize ( Ses::getUpDirTmp ($this->u()->getUsername()) . "/" . $p ['foto_fnbig'], Ses::getUpDirTmp ($this->u()->getUsername()) . "/" . $p ['foto_fnsm'], $img_p_sm );
				}
			}
		}
		return true;
	}
	
	/**
	 * 
	 */
	public function  regAction(Request $request){
		//$session = $request->getSession ();
		$user = new User();
		$form = $this->createFormBuilder($user)
		->add('email','email', array('attr' => array('maxlength'=>64,)))
		->add('pwd','password', array('attr' => array('maxlength'=>16,)))
		->getForm();
		
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid()) {
				
				//if ($orm->isSubmitted() && $form->isValid()) {
				// TODO: Persist the user entity
				$user->setStatus(0);
				$user->setUsername(Ses::before('@', $user->getEmail()));
				//$this->em()->persist($user);
				//create ui info
				//$ui = new UserInfo();
				$user->setFotoavatar('avatar.jpg');
				$user->setFotoselfi('selfi.jpg');
				//$this->em()->persist($ui);
				//create role
				$ur = $this->em()->getRepository('AppBundle:UserRole')->findOneBy(array('role'=>'ROLE_USER'));//new UserRole();
				$user->addUserRole($ur);
				//get city + country
				$city = $this->em()->getRepository('AppBundle:City')->findOneById(1);
				$user->setCity($city);
				$user->setCountry($city->getCountry());
				//ref both
				//$user->setUserinfo($ui);
				//$ui->setUser($user);
				//copy avatar.jpg & selfi.jpg to user folder
				if (!file_exists(Ses::getUpDirTmp($user->getUsername()).'/')) {
					mkdir(Ses::getUpDirTmp($user->getUsername()).'/', 0777, true);
				}
				copy(Ses::getUpDirImg().'/avatar.jpg', Ses::getUpDirTmp($user->getUsername()).'/avatar.jpg');
				copy(Ses::getUpDirImg().'/selfi.jpg', Ses::getUpDirTmp($user->getUsername()).'/selfi.jpg');
				$this->em()->persist($user);
				$this->em()->flush();
				//$session->set ( "me_reg_user",$user->getUsername() );
				//if global mail send is on
				if ($this->p("notify_off")=="1"){
					return $this->redirect($this->generateUrl('me_activate',array('username' => $user->getUsername(),'token'=>$user->getUnid())));
				}else{
					$nm = $this->get('app.notify.manager');
					if (!$nm->send(array(
							'to'=>$user->getEmail(),
							'from'=>$this->p('mail_reg_from_adr'),
							's'=>$this->p('mail_reg_subj'),
							'bn'=>'reg',
							'bo'=>array('username' => $user->getUsername(),'token'=>$user->getUnid())
					))){
						//TODO Show error-page: Mail is not send, try again
					}
				}
				//return $this->redirect($this->generateUrl('me_activate', array('user' => $user)));
				return $this->redirect($this->generateUrl('me_activate',array('username' => $user->getUsername(),'token'=>$user->getUnid())));
			}else{
				//@TODO Error hanlder write to log and show to user
				//$session->set ( "me_reg_valid",$form->getErrors() );
			}
		}
		return $form;
	}
	
	
	
	/**
	 * @param Request $request
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function showQuestion(Request $request, $id) {
		$blog = $this->em()->getRepository('AppBundle:Blog')->find($id);
		if (!$blog) {
			throw $this->createNotFoundException('Blog is not found, id='-$id);
		}
		
		//check if current blog is bookmarked by current user
		$enty_bm_by_me = array('class'=>' ','title'=>'');
		$ismy=false;
		if($this->u() != null){
			foreach ($this->u()->getBmblogs() as $bme) {
				if ($bme->getBlogo()->getId()==$id) {$enty_bm_by_me = array('class'=>' but-bookmark-active ','title'=>'');break;}
			}
			$ismy = $this->u()->getId()==$blog->getUser()->getId()?true:false;
			if(!$ismy){
				$blog->setViewcount((is_numeric($blog->getViewcount())?$blog->getViewcount()+1:1));
				$this->em()->persist($blog);
				$this->em()->flush($blog);
			}
			//is in black
			if ($this->isU1InBlackOfU2($this->u(),$blog->getUser())) {
				$blog->u_in_black=1;
			}
			
			//check the guests
			// TODO guest as AJAX after page load
			if(!$ismy) $this->setGuest($blog, $this->u(), 'Blog');
			
			//check like
			$this->setLike($blog, $this->u());
		}
		
		
		$sort = $request->get('sort');
		if ($sort == null) $sort="DESC"; 
		
		//$coms_count=0;
		$coms = $this->em()->getRepository('AppBundle:BlogCom')->getByBlog($blog->getId(),strtoupper($sort));
		//$coms_count = count($coms);
		//$coms_count += $this->getCCCount($blog->getId(),"Blog");
		
		
		$sort2 = $request->get('sort2');
		if ($sort2 == null) $sort2="DESC";
		
		
		return $this->render('AppBundle:Blog:show.html.twig', array(
				'u'=>$this->u(),
				'ismy'=>$ismy,
				'enty_bm_by_me'=>$enty_bm_by_me,
				'blog' => $blog,
				'coms' => $coms,
		));
	}
	
	
	/**
	 * @param Request $request
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function showQuiz(Request $request, $id) {
		$comy = $this->em()->getRepository('AppBundle:Comy')->find($id);
		if (!$comy) {
			throw $this->createNotFoundException('Comy is not found, id='-$id);
		}
	
		//check if current comy is bookmarked by current user
		$ismy=false;$ismember=false;
		if($this->u() != null){
			$ismy = $this->u()->getId()==$comy->getUser()->getId()?true:false;
			//is member already
			$cmember = $this->em()->getRepository('AppBundle:ComyMember')
			->findOneBy(array('user' => $this->u()->getId(),'whato'=>$id));
			if ($cmember) $ismember=true;
		}
	
	
		$sort = $request->get('sort');
		if ($sort == null) $sort="DESC";
	
		//get last comments from all comys & forums
		$coms=0;
	
	
		$sort2 = $request->get('sort2');
		if ($sort2 == null) $sort2="DESC";
		
		$comyblogs = $this->em()->getRepository('AppBundle:ComyBlog')->getLastByComy($id,strtoupper($sort),10);
		$comyforums = $this->em()->getRepository('AppBundle:ComyForum')->getLastByComy($id,strtoupper($sort),10);
		
		$comycoms = array();
		foreach ($comyblogs as $e) {
			$comycoms = array_merge($comycoms,$this->em()->getRepository ( 'AppBundle:ComyBlogCom' )->getByPAll ($e->getId(),20));
		}
	
		usort( $comycoms, function ( $a, $b ) {
			$al = $a->created;
			$bl = $b->created;
			if ($al == $bl) return 0;
			return ($al < $bl) ? +1 : -1;
		} );
		
		//$comymembers =$this->em()->getRepository('AppBundle:ComyMember')->findBy(array('user' => $this->u()->getId()));
	
		return $this->render('AppBundle:Comy:show.html.twig', array(
				'u'=>$this->u(),
				'ismy'=>$ismy,
				'ismember'=>$ismember,
				'comy' => $comy,
		        'comyblogs' => $comyblogs,
				'comyforums' => $comyforums,
				'coms' => $coms,
				'comycoms' => $comycoms,
		));
	}
	
	
	
	
	
}






/**
 * 
 */