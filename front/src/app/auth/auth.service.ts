import { Location } from '@angular/common';
import { Injectable } from '@angular/core';
import { JwtHelperService } from '@auth0/angular-jwt';
import { BehaviorSubject } from 'rxjs';

import { environment } from 'src/environments/environment';
import { Utilisateur } from './utilisateur.interface';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  utilisateurConnecte$ = new BehaviorSubject<Utilisateur>(undefined);
  utilisateurConnecte: Utilisateur;

  constructor(
    private location: Location,
    private jwtHelperService: JwtHelperService
  ) {
    this.setUtilisateurConnecte(this.getUtilisateurFromAuthToken());
  }

  public connecte() {
    if (environment.production) this.redirigeVersAuthSp();
    this.setUtilisateurConnecte(environment.utilisateurParDefaut);
  }

  private getAuthToken(): string {
    let authToken = this.getAuthTokenFromHash();
    if (!authToken) authToken = localStorage.getItem(environment.authTokenLocalStorageKey);
    return authToken;
  }

  private getAuthTokenFromHash(): string {
    let [, authToken] = this.location.path(true).split('#');
    if (authToken) {
      this.location.replaceState(this.location.path(false));
      localStorage.setItem(environment.authTokenLocalStorageKey, authToken);
    }
    return authToken;
  }

  private getUtilisateurFromAuthToken(): Utilisateur {
    let authToken = this.getAuthToken();
    if (!authToken) return;

    try {
      if (this.jwtHelperService.isTokenExpired(authToken)) throw new Error();
      return this.jwtHelperService.decodeToken(authToken);
    } catch (err) {
      localStorage.removeItem(environment.authTokenLocalStorageKey);
      return;
    }
  }

  private redirigeVersAuthSp() {
    window.location.href = this.addUrlParam(environment.authSpLoginUrl, 'RelayState', window.location.href);
  }

  private addUrlParam(url: string, key: string, value: string): string {
    let [base, paramString = ''] = url.split('?');
    let params = paramString.split('&');
    let newParam = [key, value].map(s => encodeURI(s)).join('=');
    params.push(newParam);
    paramString = params.join('&');
    return [base, paramString].join('?');
  }

  private setUtilisateurConnecte(utilisateur: Utilisateur) {
    this.utilisateurConnecte = utilisateur;
    this.utilisateurConnecte$.next(utilisateur);
  }
}