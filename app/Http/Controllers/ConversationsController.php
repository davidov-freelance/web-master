<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;
use Hash;
use Config;

use Gregwar\Image\Image;
use JWTAuth;

use App\Setting;
use App\User;
use App\Conversation;
use App\ConversationThread;
use App\Post;
use App\Notification;
use App\Helpers\RESTAPIHelper;

use Validator;
use App\Http\Requests\Frontend\ConversationAddRequest;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class ConversationsController extends ApiBaseController {


    public function create(ConversationAddRequest $request) {

        $postData               = $request->all();
        $userId                 = $request->input('sender_id');
        $userId                 = isset($userId) ? $userId : 0;

        $receiverId             = $request->input('receiver_id');
        $receiverId             = isset($receiverId) ? $receiverId : 0;

        $messageId              = $request->input('message_id');
        $messageId              = isset($messageId) ? $messageId : 0;

        $conversationId         = $request->input('conversation_id');
        $conversationId         = isset($conversationId) ? $conversationId : 0;

        $pTitle                 = 'Item';



        if($receiverId == $userId){

            return RESTAPIHelper::response('','Error','user can not send message to yourself', false);
        }


        $participantIds = array($userId,$receiverId);
        $convoThread = ConversationThread::whereIn('sender_id',$participantIds)
            ->whereIn('receiver_id',$participantIds)->first();



        if($convoThread) {

            $conversationId = $postData['conversation_id']    = $convoThread->id;
            $dataToUpdate['message']        = $postData['message'];
            $convoThread->update($dataToUpdate);

            if($receiverId == $userId) {

                if($receiverId == $convoThread->receiver_id) {

                    $postData['receiver_id']  = $convoThread->sender_id;
                } else {

                    $postData['receiver_id']  = $convoThread->receiver_id;
                }

            }


        }else {


            $conversationId =  $postData['conversation_id'] = ConversationThread::create($postData)->id;
        }

        $postObjId         = Conversation::create($postData)->id;


        /// PUSH NOTIFICATION WORK ======================================================
        $recInfo                    = User::where('id',$postData['receiver_id'])->first();
        $senderInfo                 = User::where('id',$postData['sender_id'])->first();

        $notiMessage = "You have received a new message from ".$senderInfo->first_name;
        /// SEnding Notifications
        $notification['receiver_id'] = $postData['receiver_id'];
        $notification['sender_id']   = $postData['sender_id'];
        $notification['message']     = $notiMessage;
        $notification['action_type'] = 'conversation';
        $notification['action_id']   = $postData['conversation_id'];
        Notification::create($notification);

        if($recInfo) {

            $deviceType             = $recInfo->device_type;
            $deviceToken            = $recInfo->device_token;
            $notification_status    = $recInfo->notification_status;

            if(!is_null($deviceType) && !is_null($deviceToken) && ($notification_status =='1')) {

                if($deviceType == 'android') {

                    $postArray = array('title' => 'Broadway Connected', 'message' => $notiMessage, 'sound' => 'default');
                   // $this->SendPushNotificationAndroid($deviceToken,$postArray);
                } else {

                    $apsArray =  array( 'alert' => $notiMessage,
                                        'action_id' => $notification['action_id'],
                                        'action_type'=>$notification['action_type'],
                                        'sound' => 'default');
                    
                    $this->SendPushNotification($deviceToken, $apsArray,$postData['receiver_id'] );
                }
            }
        }
        /// PUSH NOTIFICATION WORK ======================================================

        if($messageId > 0 ) {

            $postObj         = Conversation::with(['receiverInfo','senderInfo'])
                                 ->where('conversation_id',$conversationId)
                                 ->where('id', '>',$messageId)
                                 ->get();
        } else {

            $postObj         = Conversation::with(['receiverInfo','senderInfo'])->where('conversation_id',$conversationId)->get();
        }

        $responseArray['Conversation']         = $postObj;

        return RESTAPIHelper::response($responseArray,'Success', 'Message has been sent successfully');
    }

    public function userConversations(Request $request) {


        $offset                 = $request->input('offset');
        $offset                 = isset($offset) ? $offset : 0;
        $limit                  = $request->input('limit');
        $limit                  = isset($limit) ? $limit : 10;
        $userId               = $request->input('user_id');
        $conversations          = array();

        $postData               = $request->all();

        if( $userId == 0 ) {return RESTAPIHelper::response('','Error','user id is required', false); }

//        $is_authorized          = $this->checkTokenValidity($userId);
//        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }


        $totalRecords           = ConversationThread::where('sender_id',$userId)
                                ->orWhere('receiver_id',$userId)
                                ->count();

        $conversationObj        = ConversationThread::with(['receiverInfo','senderInfo'])->where('sender_id',$userId)
                                ->orWhere('receiver_id',$userId)
                                ->orderBy('updated_at', 'desc')
//                                ->offset($offset)
//                                ->limit($limit)
                                ->get();

        foreach($conversationObj as $cObj) {  $conversations[]  = $cObj; } // $this->_prepareConversationThreadResponse($cObj,$request); }

        $responseArray['Conversation']         = $conversations;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray,'Success', 'Data retrieved successfully');
    }

    public function conversationsThreadDetail(Request $request ) {


        $offset                 = $request->input('offset');
        $offset                 = isset($offset) ? $offset : 0;
        $limit                  = $request->input('limit');
        $limit                  = isset($limit) ? $limit : 10;
        $conversations          = array();


        $senderId               = $request->input('user_id');
        $receiverId             = $request->input('receiver_id');
        $postId                 = $request->input('post_id');

        $conversation_id        = $request->input('conversation_id');
        $conversation_id        = isset($conversation_id) ? $conversation_id : 0;

        if( $senderId == 0 ) {return RESTAPIHelper::response('','Error','user id is required', false); }

//        $is_authorized          = $this->checkTokenValidity($senderId);
//        if($is_authorized == 0) {return RESTAPIHelper::response('','Error','Invalid Token or User Id', false); }


        if($conversation_id == 0 ) {


            $participantIds = array($senderId,$receiverId);
            $convoThread = ConversationThread::whereIn('sender_id',$participantIds)
                ->whereIn('receiver_id',$participantIds)->first();

            if($convoThread) {
                $conversation_id = $convoThread->id;
            }
        }


        $totalRecords           = Conversation::where('conversation_id',$conversation_id)->count();

        $conversationObj                = Conversation::with(['receiverInfo','senderInfo'])->where('conversation_id',$conversation_id)
            ->orderBy('created_at', 'asc')
//            ->offset($offset)
//            ->limit($limit)
            ->get();

        foreach($conversationObj as $cObj) {  $conversations[]  = $cObj; } //   $this->_prepareConversationResponse($cObj,$request); }

        $responseArray['Conversation']         = $conversations;
        $responseArray['total_records'] = $totalRecords;

        return RESTAPIHelper::response($responseArray,'Success', 'Data retrieved successfully');
    }

    public function _prepareConversationResponse($cObj , $request) {

        $data['id']                 = $cObj['id'];
        $data['conversation_id']    = $cObj['conversation_id'];
        $data['sender_id']          = $cObj['sender_id'];
        $data['receiver_id']        = $cObj['receiver_id'];
        $data['post_id']            = $cObj['post_id'];
        $data['message']            = $cObj['message'];
        $data['created_at']         = $cObj['created_at'];
        $data['updated_at']         = $cObj['updated_at'];
        $data['receiverInfo']       = $cObj['receiverInfo'];
        $data['senderInfo']         = $cObj['senderInfo'];
        return $data;
    }

    public function _prepareConversationThreadResponse($cObj , $request) {

        dd($cObj);
        //$data['aa']        =$cObj;
        $data['id']                 = $cObj['id'];
        $data['conversation_id']    = $cObj['id'];
        $data['sender_id']          = $cObj['sender_id'];
        $data['receiver_id']        = $cObj['receiver_id'];
        $data['post_id']            = $cObj['post_id'];
        $data['message']            = $cObj['message'];
        $data['created_at']         = $cObj->created_at;
        $data['updated_at']         = $cObj['updated_at'];
        $data['receiverInfo']       = $cObj['receiverInfo'];
        $data['senderInfo']         = $cObj['senderInfo'];
        $data['productInfo']        = $cObj['productInfo'];
        return $data;
    }
}
