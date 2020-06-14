<?php

namespace App\Http\ViewComposers;

use App\Models\Document;
use App\Models\ProposalSnippet;
use Illuminate\View\View;

/**
 * ClientPortalHeaderComposer.php.
 *
 *
 */
class ProposalComposer
{

    public function compose(View $view)
    {
        $snippets = ProposalSnippet::scope()
            ->with('proposal_category')
            ->orderBy('name')
            ->get();

        $view->with('snippets', $snippets);


        $documents = Document::scope()
            ->whereNull('invoice_id')
            ->whereNull('expense_id')
            ->get();

        $data = [];
        foreach ($documents as $document) {
            $data[] = [
                'src' => $document->getProposalUrl(),
                'public_id' => $document->public_id,
            ];
        }

        $view->with('documents', $data);
    }
}
