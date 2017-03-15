<?php

namespace App\Http\Controllers\Vacancy;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FormVacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id,Guard $auth, Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendFile(Guard $auth, Request $request)
    {
        $user = User::find($auth->user()->getAuthIdentifier());
        $uploadFile = UploadFile::upFile();
        if($uploadFile==null) {
            $error = 'Необхiдний формат файлу: doc, docx, odt, rtf, txt, pdf розмiром до 2 мб.';
            return View::make('errors.uploadFileError', array(
                'error' => $error
            ));
        }
        Mail::send('emails.vacancyFile', ['user' => $user], function ($message) use ($uploadFile) {
            $company = Company::find(Vacancy::find(Input::get('id'))->company_id);
            $to = User::find($company->users_id)->email;
            $message->to($to, User::find($company->users_id)->name)->subject('Резюме по вакансії '.Vacancy::find(Input::get('id'))->position);
            $message->attach($uploadFile);
        });
        File::delete($uploadFile);
        return view('vacancy/vacancyAnswer');

    }

    public function link(Guard $auth, Request $request)
    {
        dd($request);
        $this->validate($request,[
            'Link' => 'url|required'
        ]);

        $link = Input::get('Link');
        $user = User::find($auth->user()->getAuthIdentifier());
        $company = Company::find(Vacancy::find(Input::get('id'))->company_id);
        Mail::send('emails.vacancyLink', ['user' => $user, 'link' => $link], function ($message) {
            $company = Company::find(Vacancy::find(Input::get('id'))->company_id);
            $to = User::find($company->users_id)->email;
            $message->to($to, User::find($company->users_id)->name)->subject('Резюме по вакансії '.Vacancy::find(Input::get('id'))->position);
        });


//    $link = $linkModel->fillResume(0,$auth,$request);

        //     $link ->save();
        //return view('vacancy/show');
        return view('vacancy/vacancyAnswer');
    }

    public function sendResume(Guard $auth, Request $request)
    {
        $resume = Resume::find(Input::get('resumeId'));
        $user = User::find($auth->user()->getAuthIdentifier());
        Mail::send('emails.vacancyResume', ['user' => $user, 'resume' => $resume], function ($message) {
            $company = Company::find(Vacancy::find(Input::get('id'))->company_id);
            $to = User::find($company->users_id)->email;
            $message->to($to, User::find($company->users_id)->name)->subject('Резюме по вакансії '.Vacancy::find(Input::get('id'))->position);
        });
        return view('vacancy/vacancyAnswer');
    }
}
