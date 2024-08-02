<?php

class Test extends \Inilim\Validator\ValidAbstract
{
    public const ALIAS  = [
        'responsible_user_id' => 'res_user_id',
    ];

    public const EXCEPT = [
        '__construct',
        'allocation',
        'setting',
        'checkResponsibleUser',
    ];

    function allocation()
    {
    }

    function setting(array &$data): self
    {
        $this->exec($data, [
            'responsible_user_id',
            'pipeline_id',
            'profit',
            'cost',
            'revenue',
            'filter',
            'send_leads_to_unsorted',
            'use_date_of_sale',
            'use_companies_as_client',
            'manager_for_deal_if_contact_found',
            'filtering_value',
            'form_id',
            'form_name',
        ]);

        return $this;
    }

    function checkResponsibleUser(int $res_user_id): bool
    {
        return false;
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    protected function res_user_id($key)
    {
        dde($key);
        $vdata = $this->getVData();
        $value = \_arr()->dataGet($vdata->data, $key);
        if ($value === null) {
            return;
        }

        if (!\isInt($value)) {
            $this->setError('не число', $key);
            return;
        }

        if (!$this->checkResponsibleUser(\int($value))) {
            $this->setError('Ответственный пользователь не найден', $key);
        }
    }
}
