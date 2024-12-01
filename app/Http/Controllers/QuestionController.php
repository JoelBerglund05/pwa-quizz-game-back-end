<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questions;

class QuestionController extends Controller
{
    public function getQuestion(Request $request)
    {
        $db = new Questions();
        $randomNumber = rand(1, count());
    }
}
