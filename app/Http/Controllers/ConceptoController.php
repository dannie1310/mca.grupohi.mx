<?php

namespace App\Http\Controllers;

use App\Models\Concepto;
use App\Models\Transformers\ConceptoTreeTransformer;

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
}
