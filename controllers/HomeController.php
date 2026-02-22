<?php
class HomeController extends Controller {
    
    public function index() {
        $data = [
            'title' => 'الرئيسية',
            'welcome' => 'مرحباً بك في موقع MVC'
        ];
        
        $this->view('home/index', $data);
    }

    public function about() {
        $data = ['title' => 'من نحن'];
        $this->view('page/about', $data);
    }

    public function services() {
        $data = ['title' => 'خدماتنا'];
        $this->view('page/services', $data);
    }

    public function contact() {
        $data = ['title' => 'اتصل بنا'];
        $this->view('page/contact', $data);
    }
}