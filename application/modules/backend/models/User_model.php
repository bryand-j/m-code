<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model{

  var $column_order = array();
  var $order = array('auth_user.created'=>"DESC");
  var $select = " auth_user_to_group.id_user,
                  auth_user_to_group.id_group,
                  auth_user.name,
                  auth_user.email,
                  auth_user.is_active,
                  auth_user.is_delete,
                  auth_user.created,
                  auth_group.group";

  private function _get_datatables_query()
    {
      $this->db->select($this->select);
      $this->db->from("auth_user_to_group");
      $this->db->join("auth_user","auth_user.id_user = auth_user_to_group.id_user");
      $this->db->join("auth_group","auth_group.id = auth_user_to_group.id_group","left");
      $this->db->where("auth_user.is_delete","0");
      $this->db->where("auth_user_to_group.id_user !=","1");

      if(isset($_POST['order'])) // here order processing
       {
           $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
       }
       else if(isset($this->order))
       {
           $order = $this->order;
           $this->db->order_by(key($order), $order[key($order)]);
       }

    }


    public function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->select($this->select);
        $this->db->from("auth_user_to_group");
        $this->db->join("auth_user","auth_user.id_user = auth_user_to_group.id_user");
        $this->db->join("auth_group","auth_group.id = auth_user_to_group.id_group","left");
        $this->db->where("auth_user.is_delete","0");
        $this->db->where("auth_user_to_group.id_user !=","1");
        return $this->db->count_all_results();
    }


    function get_where_data($id)
    {
      $this->db->select($this->select);
      $this->db->from("auth_user_to_group");
      $this->db->join("auth_user","auth_user.id_user = auth_user_to_group.id_user");
      $this->db->join("auth_group","auth_group.id = auth_user_to_group.id_group","left");
      $this->db->where("auth_user.is_delete","0");
      $this->db->where("auth_user_to_group.id_user !=","1");
      $this->db->where("auth_user.id_user",$id);
      return $this->db->get()->row();
    }

}
