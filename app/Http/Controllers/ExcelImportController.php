<?php

// app/Http/Controllers/ExcelImportController.php

namespace App\Http\Controllers;

use App\Http\Requests\ListeRequest;
use App\Models\Groupe;
use App\Models\Liste;
use App\Models\Membre;
use Illuminate\Http\Request;
use Matrix\Decomposition\LU;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\Group;

class ExcelImportController extends Controller
{
    public function import(Request $request,ListeRequest $listeNom)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx'
        ]);

        $file = $request->file('excel_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);

        // Lire le contenu du fichier Excel
        $spreadsheet = IOFactory::load(public_path('uploads/' . $filename));
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Récupérer le nom de la liste
        $listeName = $listeNom->validated();
        $liste = Liste::Create(
            ['nomListe' => $listeName], // Condition de mise à jour
            ['nombreMembre' => count($sheetData)] );
        // Enregistrer les membres dans la base de données
        foreach ($sheetData as $row) {
            // Sauvegarder chaque membre dans la table 'membres' par exemple
            Membre::create([
                'nom' => $row['Nom'],
                'prenom' => $row['Prenom'],
                'categorieId' => $row['Categorie'],
                'liste_id' => $liste->id, // Assurez-vous d'avoir l'id de la liste ici
            ]);
        }
        $members = Membre::where('listeId', $liste->id)->get();

        // Calculer les groupes
        $numGroups = $request->input('num_groups');
        $peoplePerGroup = $request->input('people_per_group');

        if ($numGroups) {
            // Si l'utilisateur spécifie le nombre de groupes
            $groupSize = ceil(count($members) / $numGroups);
        } elseif ($peoplePerGroup) {
            // Si l'utilisateur spécifie le nombre de personnes par groupe
            $groupSize = $peoplePerGroup;
            $numGroups = ceil(count($members) / $groupSize);
        } else {
            // Par défaut, mettre tous les membres dans un seul groupe
            $groupSize = count($members);
            $numGroups = 1;
        }

        // Diviser les membres en groupes
        $groups = array_chunk($members->toArray(), $groupSize);

        // Enregistrer les groupes dans la base de données
        foreach ($groups as $index => $group) {
            $groupName = $listeName . ' - Groupe ' . ($index + 1);

            // Créer le groupe
            $groupId = Groupe::create([
                'nom' => $groupName,
                'listeId' => $liste->id,
            ])->id;

            // Associer les membres au groupe
            foreach ($group as $member) {
                Membre::where('id', $member['id'])->update(['groupeId' => $groupId]);
            }
        }
        

        // Supprimer le fichier téléchargé
        unlink(public_path('uploads/' . $filename));

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Importation réussie.');
    }
    public function create(){
        return view("");
    }
}
