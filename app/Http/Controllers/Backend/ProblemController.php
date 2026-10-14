<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Problem\Problem;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Backend\Auth\Service\ManageProblemRequest;
use App\Models\Problem\Groupproblem;

class ProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManageProblemRequest $request)
    {
      $problems = Problem::orderBy('problem','asc')->paginate(15);


      return view('backend.problems.index',compact('problems'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ManageProblemRequest $request)
    {
        $groupproblems = Groupproblem::all()->pluck('name', 'id');
        return view('backend.problems.addnew',compact('groupproblems'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,ManageProblemRequest $request1)
    {

      $this->validator($request->all())->validate();
      $group_problem= Groupproblem::where('id',request('groupproblem'))->first();
      $problem = new Problem();
      $problem->problem = request('problem');
      $problem->description = request('description');
      $problem->save();
      $problem->group_problem()->attach($group_problem);

      return redirect()->route('admin.problem.create')->withFlashSuccess(__('alerts.backend.problem.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,ManageProblemRequest $request)
    {
      $problem = Problem::find($id);
      $groupproblems = Groupproblem::all()->pluck('name', 'id');
      return view('backend.problems.edit',compact('problem','groupproblems'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $input = $request->all();
      $this->validate($request, [
          'description'=>'nullable',
          'problem' => 'required|unique:problems,problem,'.$id,
      ]);
        $group_problem= Groupproblem::where('id',request('groupproblem'))->first();

        $problem = Problem::find($id);
        $problem->group_problem()->sync($group_problem);
        $problem->update($input);

        return redirect()->route('admin.problem.index')
                        ->withFlashSuccess(__('alerts.backend.problem.updated'));

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'groupproblem' =>'required',
            'problem' => ['required', 'string', 'max:255', 'unique:problems'],
            'decription' => 'nullable',
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

      $services = Problem::find($id)->service();

      if($services->count()>0)
        return redirect()->route('admin.problem.index')
                      ->withFlashDanger(__('alerts.backend.problem.warning_delete'));

      $problem = Problem::findOrFail($id);
      $group_problem=$problem->group_problem();
      $problem->delete();
      $group_problem->detach();
      return redirect()->route('admin.problem.index')
                      ->withFlashSuccess(__('alerts.backend.problem.deleted'));
    }
}
