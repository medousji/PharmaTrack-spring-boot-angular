import { HttpParams } from '@angular/common/http';

/** Convertit un objet en HttpParams en ignorant les valeurs vides (null/''/undefined). */
export function toHttpParams(values: object): HttpParams {
  let p = new HttpParams();
  for (const [k, v] of Object.entries(values)) {
    if (v !== undefined && v !== null && v !== '') {
      p = p.set(k, String(v));
    }
  }
  return p;
}