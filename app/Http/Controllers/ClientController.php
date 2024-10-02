<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with('project_data')->get();
        return view('clients.list', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $client = new Client;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:clients',
        ]);

        if ($validator->passes()) {
            $client->name = $request->name;
            $client->email = $request->email;
            $client->save();

            return redirect(route('clients.index'))->with('success', 'Client Added Successfully');
        } else {
            return redirect()->route('clients.create')->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:clients,email,' . $id . ',id',
        ]);

        if ($validator->passes()) {
            $client->name = $request->name;
            $client->email = $request->email;
            $client->save();

            return redirect(route('clients.index'))->with('success', 'Client Updated Successfully');
        } else {
            return redirect()->route('clients.edit')->withInput()->withErrors($validator);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        if (!is_null($client)) {
            $client->delete();
            return redirect(route('clients.index'))->with('success', 'Client Deleted Successfully');
        }
    }
}
