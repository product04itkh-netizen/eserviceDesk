<?php

namespace App\Models\Auth\Traits\Attribute;


/**
 * Trait RoleAttribute.
 */
trait ServiceAttribute
{
    /**
     * @return string
     */
    public function getEditButtonAttribute()
    {
      if($this->isServiceAdmin()){
        $disabled='';
      }else {
        //if(($this->payment=='0') || empty($this->payment)){
            if($this->isServiceEnginner() && Auth()->user()->id==$this->assignto){
              if(in_array(strtolower($this->status),array('delivered')))
                $disabled='disabled';
              else
                $disabled='';
            }elseif($this->isServiceAdmin())
              $disabled='';
            elseif($this->isServiceReceptionist()){
                if(in_array(strtolower($this->status),array('delivered','start','not start','pending','progress')))
                  $disabled='disabled';
                else
                  $disabled='';
            }else
              $disabled='disabled';
        //}else
          //$disabled='disabled';

      }

      //  return '<a href="'.route('admin.service.edit', $this).'" class="btn btn-primary ' . $disabled . '"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'"></i></a>';
return '
      <form method="post" action="' . route('admin.service.edit',[$this]) . '" enctype="multipart/form-data">
        ' .   csrf_field() . '
        <input type="hidden" name="_method" value="PATCH">
        <button type="submit" class="btn btn-sm btn-primary ' . $disabled . '"><i class="fas fa-edit"></i></button>
    </form>';

    }

    public function getPrintButtonAttribute()
    {
        return '<a href="'.route('admin.service.receipt', $this).'" class="btn btn-info"><i class="fas fa-print" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.print').'"></i></a>';
    }

    public function getShowButtonAttribute()
    {
        return '<a href="'.route('admin.service.show', $this).'" class="btn btn-info"><i class="fas fa-eye" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.view').'"></i></a>';
    }

    public function getInvoiceButtonAttribute()
    {
          return '<a href="'. route('admin.service.invoice', $this) .'" class="btn btn-info"><i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.invoice').'"></i></a>';
    }
    /**
     * @return string
     */
    public function getDeleteButtonAttribute()
    {
      if((($this->payment=='0') || empty($this->payment)) && !$this->isServiceReceptionist())
          $disabled='';
        else
          $disabled='disabled';

          return '<a href="'.route('admin.service.destroy', $this).'"
			 data-method="delete"
			 data-trans-button-cancel="'.__('buttons.general.cancel').'"
			 data-trans-button-confirm="'.__('buttons.general.crud.delete').'"
			 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
			 class="btn btn-danger ' . $disabled .'"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.delete').'"></i></a> ';
    }

    public function getAssignButtonAttribute()
    {
        if(in_array($this->payment,array('pending')) && empty($this->assignto))
          $disabled='';
        else
          $disabled='disabled';



          return '<a href="'.route('admin.service.assignto', $this).'"
			 data-method="assign"
			 data-trans-button-cancel="'.__('buttons.general.cancel').'"
			 data-trans-button-confirm="'.__('buttons.general.crud.assignto').'"
			 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
			 class="btn btn-primary ' . $disabled .'"><i class="fas fa-wrench" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.assignto').'"></i></a> ';
    }


    /**
     * @return string
     */
    public function getActionButtonsAttribute()
    {
      //  if ($this->id == 1) {
        //    return 'N/A';
      //  }

        return '<div class="btn-group btn-group-sm" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
        '. $this->show_button.'
        '. (($this->isServiceAdmin() || $this->isServiceReceptionist())?$this->print_button : '') .'
        '. (($this->isServiceAdmin() || $this->isServiceReceptionist())?$this->invoice_button : '') .'
        '. $this->edit_button.'
			  '. (($this->isServiceAdmin() || $this->isServiceReceptionist())?$this->delete_button : '') .'
        '. (($this->isServiceEnginner())?$this->assign_button : '') .'
			</div>';
      //(($this->isServiceReceptionist())?$this->delete_button:'')
    }
}
