import { Utilisateur } from "../interfaces/utilisateur.interface";

export class PortHabilitations {

  static ROLE_PARTICIPANT: string = 'TKDO_PARTICIPANT';
  
  static DROIT_CONNEXION: string = 'CONNEXION';
  static DROIT_VOIR_MENUS_PARTICIPANT: string = 'DROIT_VOIR_MENUS_PARTICIPANT';

  /**
   * La matrice rôles/droits
   */
  private static matrice: { [role: string]: string[] } = {
    [PortHabilitations.ROLE_PARTICIPANT]: [
      PortHabilitations.DROIT_CONNEXION,
      PortHabilitations.DROIT_VOIR_MENUS_PARTICIPANT
    ]
  }

  /**
   * Vérifie qu'un utilisateur a un droit donné.
   */
  hasDroit(droit: string, utilisateur: Utilisateur): boolean {
    return utilisateur.roles
      .map(role => PortHabilitations.matrice[role])
      .some(droits => droits.includes(droit));
  }

  /**
   * Vérifie qu'un rôle est bien connu de l'application.
   */
  estRoleConnu(role: string): boolean {
    return !!PortHabilitations.matrice[role];
  }
}
