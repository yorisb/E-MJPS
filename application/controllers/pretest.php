<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pretest extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Pre-test';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules('nis', 'nis', 'required|trim');
        $this->form_validation->set_rules('nama', 'nama', 'required|trim');
        $this->form_validation->set_rules('file', 'file', 'required|trim');
        $this->form_validation->set_rules('fitur', 'fitur', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pretest/index', $data);
            $this->load->view('templates/footer');
        } else {

            $config['allowed_types'] = 'doc|docx|pdf';
            $config['max_size']      = 0;
            $config['upload_path']   = './upload/tugas';

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $data = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">'
                        . $this->upload->display_errors() .
                        '</div>'
                );
                redirect('pretest');
            }


            $data = [

                'nis' => htmlspecialchars($this->input->post('nis', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'file' => htmlspecialchars($this->input->post('file', true)),
                'fitur' => htmlspecialchars($this->input->post('fitur', true)),
                'date_created' => time()
            ];


            $this->db->insert('tugas_pretest', $data);



            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">
            Profile telah berhasil diedit !
          </div>'
            );
            redirect('pretest');
        }
    }
    public function upload()
    {
        $config['upload_path'] = './upload/tugas/pretest';
        $config['allowed_types'] = 'doc|docx|pdf';
        $config['max_size'] = 0;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {

            $fileData = $this->upload->data();

            $upload = [
                'nis' => htmlspecialchars($this->input->post('nis', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'file' => $fileData['file_name'],
                'fitur' => htmlspecialchars($this->input->post('fitur', true)),
                'date_upload' => date('Y-m-d')
            ];

            if ($this->db->insert('tugas_pretest', $upload)) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-success" role="alert">
                File telah di Upload  !
              </div>'
                );
                redirect('pretest');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('pretest');
            }
        }
    }
}
