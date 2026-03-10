@extends('layouts.app')

@section('title', 'Canevas ' . $semaine->label())

@section('content')

    {{-- ── En-tête semaine ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 no-print">
        <div class="d-flex align-items-center gap-2">
            @if ($previousSemaine)
                <a href="{{ route('semaines.show', $previousSemaine) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-chevron-left"></i> Semaine précédente
                </a>
            @endif

            <h5 class="mb-0 mx-2">
                <i class="fas fa-calendar-week text-primary me-2"></i>
                <strong>{{ $semaine->label() }}</strong>
            </h5>

            @if ($nextSemaine)
                <a href="{{ route('semaines.show', $nextSemaine) }}" class="btn btn-warning btn-sm">
                    Semaine suivante <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>

        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
            @auth
                <form method="POST" action="{{ route('semaines.creerSuivante', $semaine) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm"
                        onclick="return confirm('Créer la semaine suivante avec les activités planifiées ?')">
                        <i class="fas fa-forward me-2"></i>Créer semaine suivante
                    </button>
                </form>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNouvelleSemaine">
                    <i class="fas fa-plus me-2"></i>Nouvelle semaine
                </button>
            @endauth
        </div>
    </div>

    {{-- ── Table canevas ────────────────────────────────────────────────── --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered canevas-table mb-0 w-100">
            <thead>
                <tr>
                    <th style="width:130px">Acteurs</th>
                    <th>Activités importantes de la semaine s'achevant<br>
                        <small class="fw-normal opacity-75">{{ $semaine->periodeCourante() }}</small>
                    </th>
                    <th style="width:100px">Statut activité</th>
                    <th class="th-detail">
                        Détails / Précisions<br>
                        <small class="fw-normal" style="font-size:9px">
                            activités non terminée : dire pourquoi ;<br>si en cours : préciser le taux d'avancement
                        </small>
                    </th>
                    <th>Activités importantes à venir – semaine à venir<br>
                        <small class="fw-normal opacity-75">{{ $semaine->periodeSuivante() }}</small>
                    </th>
                    <th style="width:140px">Obstacles à lever / besoins pour avancer</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $employe = $item['employe'];
                        $courantes = $item['courantes'];
                        $suivantes = $item['suivantes'];
                        $maxRows = $item['maxRows'];
                        $obstacles = $item['obstacles'];
                    @endphp

                    @for ($i = 0; $i < $maxRows; $i++)
                        @php
                            $courante = $courantes[$i] ?? null;
                            $suivante = $suivantes[$i] ?? null;
                        @endphp
                        <tr class="{{ $i === 0 ? 'sep-employe' : '' }}">
                            {{-- Acteurs (rowspan) --}}
                            @if ($i === 0)
                                <td rowspan="{{ $maxRows }}" class="td-acteur">
                                    <div>{{ $employe->nom }}</div>
                                    @if ($employe->poste)
                                        <div class="poste">{{ $employe->poste }}</div>
                                    @endif
                                    <div class="action-row no-print">
                                        <button class="btn btn-xs btn-outline-primary"
                                            onclick="openAddModal({{ $employe->id }}, 'courante')"
                                            title="Ajouter activité semaine courante">
                                            <i class="fas fa-plus"></i> S.Courante
                                        </button>
                                        <button class="btn btn-xs btn-outline-success"
                                            onclick="openAddModal({{ $employe->id }}, 'suivante')"
                                            title="Ajouter activité semaine suivante">
                                            <i class="fas fa-plus"></i> S.Suivante
                                        </button>
                                    </div>
                                </td>
                            @endif

                            {{-- Activité courante --}}
                            <td class="td-activite">
                                @if ($courante)
                                    <span>{{ $courante->description }}</span>
                                    <div class="no-print mt-1">
                                        <button class="btn btn-xs btn-outline-secondary"
                                            onclick="openEditModal({{ $courante->id }}, {{ json_encode($courante->description) }}, {{ json_encode($courante->statut) }}, {{ json_encode($courante->raison) }}, 'courante')"
                                            title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <form method="POST" action="{{ route('activites.destroy', $courante) }}"
                                            class="d-inline" onsubmit="return confirm('Supprimer cette activité ?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>

                            {{-- Statut (dropdown auto-submit) --}}
                            <td class="text-center align-middle" style="min-width:105px">
                                @if ($courante)
                                    <form method="POST" action="{{ route('activites.statut', $courante) }}">
                                        @csrf @method('PATCH')
                                        <select name="statut" class="statut-select"
                                            data-color="{{ $courante->statut ? $courante->statutColor() : '#6c757d' }}"
                                            onchange="this.form.submit()">
                                            <option value="">– statut –</option>
                                            @foreach (\App\Models\Activite::STATUTS as $val => $label)
                                                <option value="{{ $val }}"
                                                    data-color="{{ \App\Models\Activite::STATUT_COLORS[$val] }}"
                                                    {{ $courante->statut === $val ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </td>

                            {{-- Détails / Raison --}}
                            <td class="td-raison">
                                @if ($courante && $courante->raison)
                                    {{ $courante->raison }}
                                    <button class="btn btn-xs btn-link p-0 no-print"
                                        onclick="openRaisonModal({{ $courante->id }}, {{ json_encode($courante->description) }}, {{ json_encode($courante->statut) }}, {{ json_encode($courante->raison) }})"
                                        title="Modifier la précision">
                                        <i class="fas fa-edit text-muted"></i>
                                    </button>
                                @elseif ($courante && $courante->necessiteRaison())
                                    <span class="text-danger small">
                                        <i class="fas fa-exclamation-triangle"></i> Précision manquante
                                    </span>
                                    @auth
                                        <button class="btn btn-xs btn-outline-warning no-print"
                                            onclick="openRaisonModal({{ $courante->id }}, {{ json_encode($courante->description) }}, {{ json_encode($courante->statut) }}, '')">
                                            Ajouter
                                        </button>
                                    @endauth
                                @endif
                            </td>

                            {{-- Activité suivante --}}
                            <td class="td-activite-suivante">
                                @if ($suivante)
                                    <span>{{ $suivante->description }}</span>
                                    <div class="no-print mt-1">
                                        <button class="btn btn-xs btn-outline-secondary"
                                            onclick="openEditModal({{ $suivante->id }}, {{ json_encode($suivante->description) }}, null, null, 'suivante')"
                                            title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <form method="POST" action="{{ route('activites.destroy', $suivante) }}"
                                            class="d-inline" onsubmit="return confirm('Supprimer cette activité ?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>

                            {{-- Obstacles (rowspan) --}}
                            @if ($i === 0)
                                <td rowspan="{{ $maxRows }}" class="td-obstacles">
                                    @if ($obstacles)
                                        <span class="ras">{{ $obstacles }}</span>
                                    @endif
                                    <div class="no-print mt-2">
                                        <button class="btn btn-xs btn-outline-secondary"
                                            onclick="openObstaclesModal({{ $employe->id }}, {{ json_encode($obstacles ?? '') }})">
                                            <i class="fas fa-edit"></i>
                                            {{ $obstacles ? '' : 'Ajouter' }}
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endfor
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                            Aucun employé actif. <a href="{{ route('employes.create') }}">Ajouter un employé</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ════════════════════════════════════════════════════════
     MODALS
     ════════════════════════════════════════════════════════ --}}

    {{-- Modal : Ajouter activité (multi-lignes) --}}
    <div class="modal fade" id="modalAjout" tabindex="-1" aria-labelledby="modalAjoutLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAjoutLabel">Ajouter activité</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAjout" method="POST" action="{{ route('activites.store') }}">
                    @csrf
                    <input type="hidden" name="employe_id" id="ajoutEmployeId">
                    <input type="hidden" name="semaine_id" value="{{ $semaine->id }}">
                    <input type="hidden" name="type" id="ajoutType">
                    <div class="modal-body" style="max-height:65vh;overflow-y:auto">
                        <div id="ajoutLignesContainer"></div>
                        <div class="d-flex justify-content-end">
                            <button type="button" id="btnAjoutLigne" class="btn btn-outline-primary btn-sm mt-1">
                                <i class="fas fa-plus me-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal : Modifier activité --}}
    <div class="modal fade" id="modalActivite" tabindex="-1" aria-labelledby="modalActiviteLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalActiviteLabel">Modifier activité</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formActivite" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="type" id="activiteType">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="activiteDescription" class="form-control" rows="3" required
                                placeholder="Décrivez l'activité…"></textarea>
                        </div>
                        <div id="divStatut" class="mb-3">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" id="activiteStatut" class="form-select">
                                <option value="">– Sélectionner –</option>
                                @foreach (\App\Models\Activite::STATUTS as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="divRaison" class="mb-3" style="display:none">
                            <label class="form-label fw-semibold text-danger">
                                Précision / Raison <span class="text-danger">*</span>
                            </label>
                            <textarea name="raison" id="activiteRaison" class="form-control" rows="2"
                                placeholder="Pourquoi non terminé ? Taux d'avancement ?"></textarea>
                        </div>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal : Obstacles --}}
    <div class="modal fade" id="modalObstacles" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Obstacles / Besoins</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formObstacles" method="POST" action="{{ route('semaines.obstacles', $semaine) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="employe_id" id="obstaclesEmployeId">
                    <div class="modal-body">
                        <textarea name="obstacles" id="obstaclesTexte" class="form-control" rows="4"
                            placeholder="Ex : RAS, Besoin d'un ordinateur…"></textarea>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-warning btn-sm text-dark">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal : Précision/Raison rapide --}}
    <div class="modal fade" id="modalRaison" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-comment-alt me-2"></i>Précision / Raison</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRaison" method="POST" action="">
                    @csrf @method('PUT')
                    <input type="hidden" name="description" id="raisonDescription">
                    <input type="hidden" name="statut" id="raisonStatut">
                    <div class="modal-body">
                        <label class="form-label small fw-semibold">Taux d'avancement ou raison de non-réalisation</label>
                        <textarea name="raison" id="raisonTexte" class="form-control" rows="3"
                            placeholder="Ex : 50% de mise en œuvre ; En attente de validation…"></textarea>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-danger btn-sm">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal : Nouvelle semaine --}}
    <div class="modal fade" id="modalNouvelleSemaine" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouvelle semaine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('semaines.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Choisir une date dans la semaine</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // ── Statut select colors ────────────────────────────────────────────
        document.querySelectorAll('.statut-select').forEach(function(sel) {
            applyStatutColor(sel, sel.dataset.color);
            sel.addEventListener('change', function() {
                const opt = sel.options[sel.selectedIndex];
                applyStatutColor(sel, opt.dataset.color || '#6c757d');
            });
        });

        function applyStatutColor(sel, color) {
            if (!color) color = '#6c757d';
            sel.style.backgroundColor = color;
            sel.style.color = '#fff';
        }

        // ── Modal Ajout multi-lignes ────────────────────────────────────────
        const STATUTS_OPTIONS = @json(\App\Models\Activite::STATUTS);
        let ajoutType = 'courante';

        function buildLigneHTML(index) {
            const isCourante = ajoutType === 'courante';
            let statutOptions = '<option value="">– Statut –</option>';
            for (const [val, label] of Object.entries(STATUTS_OPTIONS)) {
                statutOptions += `<option value="${val}">${label}</option>`;
            }
            return `
            <div class="activite-ligne border rounded p-2 mb-2 position-relative">
                <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-ligne position-absolute top-0 end-0 m-1" title="Supprimer cette ligne">
                    <i class="fas fa-times"></i>
                </button>
                <div class="mb-2">
                    <label class="form-label fw-semibold small mb-1">Description <span class="text-danger">*</span></label>
                    <textarea name="descriptions[]" class="form-control form-control-sm" rows="2" required
                        placeholder="Décrivez l'activité…"></textarea>
                </div>
                ${isCourante ? `
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small mb-1">Statut</label>
                        <select name="statuts[]" class="form-select form-select-sm statut-ligne-select">
                            ${statutOptions}
                        </select>
                    </div>
                    <div class="col-md-7 div-raison-ligne" style="display:none">
                        <label class="form-label fw-semibold small mb-1 text-danger">Précision <span class="text-danger">*</span></label>
                        <textarea name="raisons[]" class="form-control form-control-sm" rows="1"
                            placeholder="Taux d'avancement ou raison…"></textarea>
                    </div>
                </div>` : `<input type="hidden" name="statuts[]" value=""><input type="hidden" name="raisons[]" value="">`}
            </div>`;
        }

        function addLigneToContainer() {
            const container = document.getElementById('ajoutLignesContainer');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', buildLigneHTML(index));
            const lastLigne = container.lastElementChild;

            // Bouton supprimer
            lastLigne.querySelector('.btn-suppr-ligne').addEventListener('click', function() {
                if (container.children.length > 1) {
                    lastLigne.remove();
                }
            });

            // Afficher/masquer raison selon statut
            const statutSel = lastLigne.querySelector('.statut-ligne-select');
            if (statutSel) {
                statutSel.addEventListener('change', function() {
                    const divRaison = lastLigne.querySelector('.div-raison-ligne');
                    const show = this.value && this.value !== 'fait';
                    divRaison.style.display = show ? 'block' : 'none';
                    if (!show) divRaison.querySelector('textarea').value = '';
                });
            }
        }

        document.getElementById('btnAjoutLigne').addEventListener('click', addLigneToContainer);

        function openAddModal(employeId, type) {
            ajoutType = type;
            document.getElementById('modalAjoutLabel').textContent =
                'Ajouter activité – ' + (type === 'courante' ? 'Semaine courante' : 'Semaine suivante');
            document.getElementById('ajoutEmployeId').value = employeId;
            document.getElementById('ajoutType').value = type;

            // Vider et ajouter une ligne initiale
            document.getElementById('ajoutLignesContainer').innerHTML = '';
            addLigneToContainer();

            new bootstrap.Modal(document.getElementById('modalAjout')).show();
        }

        // ── Modal Activité (Edit) ───────────────────────────────────────────
        function openEditModal(id, description, statut, raison, type) {
            document.getElementById('modalActiviteLabel').textContent = 'Modifier activité';
            document.getElementById('formActivite').action = '/activites/' + id;
            document.getElementById('activiteDescription').value = description;
            document.getElementById('activiteStatut').value = statut || '';
            document.getElementById('activiteRaison').value = raison || '';
            document.getElementById('activiteType').value = type;

            const showStatut = type === 'courante';
            document.getElementById('divStatut').style.display = showStatut ? 'block' : 'none';
            document.getElementById('divRaison').style.display =
                (statut && statut !== 'fait') ? 'block' : 'none';

            new bootstrap.Modal(document.getElementById('modalActivite')).show();
        }

        // Afficher/masquer raison selon statut sélectionné (modal edit)
        document.getElementById('activiteStatut').addEventListener('change', function() {
            const divRaison = document.getElementById('divRaison');
            const show = this.value && this.value !== 'fait';
            divRaison.style.display = show ? 'block' : 'none';
            if (!show) document.getElementById('activiteRaison').value = '';
        });

        // ── Modal Obstacles ─────────────────────────────────────────────────
        function openObstaclesModal(employeId, texte) {
            document.getElementById('obstaclesEmployeId').value = employeId;
            document.getElementById('obstaclesTexte').value = texte || '';
            new bootstrap.Modal(document.getElementById('modalObstacles')).show();
        }

        // ── Modal Raison rapide ─────────────────────────────────────────────
        function openRaisonModal(id, description, statut, raison) {
            const form = document.getElementById('formRaison');
            form.action = '/activites/' + id;
            document.getElementById('raisonDescription').value = description || '';
            document.getElementById('raisonStatut').value = statut || '';
            document.getElementById('raisonTexte').value = raison || '';
            new bootstrap.Modal(document.getElementById('modalRaison')).show();
        }
    </script>
@endsection
