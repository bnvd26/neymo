<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class AccountController extends ApiController
{
    /**
     * @Route("/api/particular/account", name="api_particular_account", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Show account details of a particular"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="account-details")
     */
    public function accountParticularState()
    {
        if (!$this->getUser()->isParticular()) {
            return $this->responseOk([
                'Information' => "Il n y a pas de compte particulier pour cet utilisateur",
                ]);
        };

        return $this->responseOk([
            'account_id' => $this->getUser()->getParticular()->getAccount()->getId(),
            'available_cash' => $this->getUser()->getParticular()->getAccount()->getAvailableCash(),
            'account_number' => $this->getUser()->getParticular()->getAccount()->getAccountNumber()
            ]);
    }

    /**
     * @Route("/api/company/account", name="api_company_account", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Show account details of a company"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Tag(name="account-details")
     */
    public function accountCompanyState()
    {
        if (!$this->getUser()->isCompany()) {
            return $this->responseOk([
                'Information' => "Il n y a pas de compte professionel pour cet utilisateur",
                ]);
        }

        $company = $this->getUser()->getCompany();

        $companyAccount = [
                'account_id' => $company->getAccount()->getId(),
                'available_cash' => $company->getAccount()->getAvailableCash(),
                'account_number' => $company->getAccount()->getAccountNumber()
        ];

        return $this->responseOk($companyAccount);
    }
}