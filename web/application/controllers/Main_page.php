<?php

use Model\Boosterpack_model;
use Model\Comment_model;
use Model\Login_model;
use Model\Post_model;
use Model\User_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{
    public function __construct()
    {

        parent::__construct();
        $this->load->library('form_validation');


        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation_many(Post_model::get_all(), 'default');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_boosterpacks()
    {
        $posts =  Boosterpack_model::preparation_many(Boosterpack_model::get_all(), 'default');
        return $this->response_success(['boosterpacks' => $posts]);
    }

    public function login()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
        $this->form_validation->set_rules("password", "password", "trim|required");

        if(!$this->form_validation->run()) throw new Exception(validation_errors());

        if(User_model::is_logged())
        {
            $this->response_success(['user' => User_model::preparation(User_model::get_user(), 'default')]);
        }

        $user = Login_model::login($this->input->post());

        return $this->response_success(['user' => User_model::preparation($user, 'default')]);
    }

    public function logout()
    {
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        Login_model::logout();
        return redirect('/main_page/index');
    }

    public function comment()
    {
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $this->form_validation->set_rules("commentText", "commentText", "trim|required|min_length[1]");
        $this->form_validation->set_rules("postId", "postId", "required|integer");
        $this->form_validation->set_rules("replyId", "replayId", "nullable|integer");

        if(!$this->form_validation->run()) throw new Exception(validation_errors());

        $replyId = $this->input->post()['replyId'];
        $data = [
            'user_id' => User_model::get_user()->get_id(),
            'assign_id' => $this->input->post()['postId'],
            'text' => $this->input->post()['commentText'],
            'reply_id' => $replyId != null ? $replyId : null,
            'likes' => 0,
        ];

        Comment_model::create($data);
        return $this->response_success();
}

    public function like_comment(int $comment_id)
    {
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $comment = Comment_model::get_by_id($comment_id);
        $comment->increment_likes(User_model::get_user());
        return $this->response_success(['comment' => Comment_model::preparation($comment, 'default')]);
    }

    public function like_post(int $post_id)
    {
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $post = Post_model::get_by_id($post_id);
        $post->increment_likes(User_model::get_user());
        return $this->response_success(['post' => Post_model::preparation($post, 'default')]);
    }

    public function add_money()
    {
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $sum = (float)App::get_ci()->input->post('sum');
        $user = User_model::get_user();
        $user->add_money($sum);
        $this->response_success(['user' => User_model::preparation(User_model::get_user(), 'default')]);
    }

    public function get_post(int $post_id) {
        $post = Post_model::get_by_id($post_id);
        return $this->response_success(['post' => Post_model::preparation($post, 'full_info')]);
    }

    public function buy_likes()
    {
        $likesCount = App::get_ci()->input->post('likes_count');
        $user = User_model::get_user();
        if(!$user->buyLikes($likesCount))
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_INTERNAL_ERROR. ' not enough money');
        }
        $this->response_success(['user' => User_model::preparation(User_model::get_user(), 'default')]);
    }

    public function buy_boosterpack()
    {
        $this->form_validation->set_rules("postId", "postId", "required|integer");
        $this->form_validation->set_rules("replyId", "replayId", "nullable|integer"); // Проверка на существование в базе?

        if(!$this->form_validation->run()) throw new Exception(validation_errors());

        // Check user is authorize
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        // TODO: task 5, покупка и открытие бустерпака
    }

    /**
     * @return object|string|void
     */
    public function get_boosterpack_info(int $bootserpack_info)
    {
        // Check user is authorize
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }


        //TODO получить содержимое бустерпака
    }


}
