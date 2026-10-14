<?php

namespace App\Models\Auth\Traits\Attribute;

/**
 * Trait RoleAttribute.
 */
trait SaleAttribute
{
    /**
     * @return string
     */
    public function getEditButtonAttribute()
    {
      if($this->payment==0)
        $disabled='';
      else
        $disabled='disabled';

        return '<a href="'.route('admin.saleorder.edit', $this).'" class="btn btn-primary ' . $disabled . '"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'"></i></a>';
    }

    /**
     * @return string
     */
    public function getDeleteButtonAttribute()
    {
        return '<a href="'.route('admin.saleorder.destroy', $this).'"
			 data-method="delete"
			 data-trans-button-cancel="'.__('buttons.general.cancel').'"
			 data-trans-button-confirm="'.__('buttons.general.crud.delete').'"
			 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
			 class="btn btn-danger"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.delete').'"></i></a> ';
    }

    public function getInvoiceButtonAttribute()
    {
          return '<a href="'. route('admin.saleorder.show', $this) .'" class="btn btn-info"><i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.invoice').'"></i></a>';
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
			  '.$this->edit_button.'
			  '.$this->delete_button.'
        '.$this->invoice_button.'
			</div>';
    }
}
