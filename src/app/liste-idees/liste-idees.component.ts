import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { Observable, combineLatest, BehaviorSubject } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import { BackendService, ListeIdees } from '../backend.service';

@Component({
  selector: 'app-liste-idees',
  templateUrl: './liste-idees.component.html',
  styleUrls: ['./liste-idees.component.scss']
})
export class ListeIdeesComponent implements OnInit {
  
  listeIdees$: Observable<ListeIdees>;
  formAjout = this.fb.group({
    desc: ['', Validators.required],
  });

  private idUtilisateur: number;
  private actualisation$ = new BehaviorSubject(true);

  constructor(
    private readonly fb: FormBuilder,
    private readonly backend: BackendService,
    private readonly route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.listeIdees$ = combineLatest(
      this.route.paramMap,
      this.actualisation$      
    ).pipe(
      switchMap(([params]) => {
        this.idUtilisateur = +params.get('idUtilisateur');
        return this.backend.getListeIdees$(this.idUtilisateur);
      })
    );
    this.actualise();
  }

  actualise() {
    this.actualisation$.next(true);
  }

  async ajoute() {
    const { desc } = this.formAjout.value;
    await this.backend.ajouteIdee(this.idUtilisateur, desc);
    this.formAjout.reset();
    this.actualise();
  }

  async supprime(idIdee: number) {
    await this.backend.supprimeIdee(this.idUtilisateur, idIdee);
    this.actualise();
  }
}
