<?php
/**
* CommonPostRequest.php - Request file
*
* This file is part common support.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Support;

use App\Yantrana\Support\CommonPostRequest;

class CommonUnsecuredPostRequest extends CommonPostRequest
{

    /**
     * Set if you need form request secured.
     *------------------------------------------------------------------------ */
    protected $securedForm = false;
}
