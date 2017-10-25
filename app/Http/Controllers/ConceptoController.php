<?php

namespace App\Http\Controllers;

use App\Models\Concepto;
use App\Models\Transformers\ConceptoTreeTransformer;
use Illuminate\Http\Request;

class ConceptoController extends Controller
{
    /**
     * ConceptoController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('context');
    }


    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function getRoot()
    {
        $roots = Concepto::getNivelesRaiz();
        $resp=ConceptoTreeTransformer::transform($roots);
        return response()->json($resp, 200);

    }

    public function getNode($id)
    {
        $concepto = Concepto::find($id);

        $node = $concepto->getHijos();


        $resp=ConceptoTreeTransformer::transform($node);

        // $data = Fractal::createData($resource);
        return response()->json($resp, 200);

    }

    /**
     * Muestra una lista de los conceptos del presupuesto de obra
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request)
    {
        if ($request->has('q')) {
            $conceptos = Concepto::search($request->get('q'));

            return response()->json(['conceptos' => $conceptos]);
        }
    }
}
