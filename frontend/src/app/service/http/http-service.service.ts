import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { catchError, map, tap } from 'rxjs/operators';
import { Observable, of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class HttpServiceService {

  private url: string

  private httpOptions

  constructor(private http: HttpClient) {
    this.url = 'http://51.77.113.245:8741/';

    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    }
  }

   /**
   * Handle Http operation that failed.
   * Let the app continue.
   *
   * @param operation - name of the operation that failed
   * @param result - optional value to return as the observable result
   */
  private handleError<T>(operation = 'operation', result?: T) {
      return (error: any): Observable<T> => {

        // TODO: send the error to remote logging infrastructure
        console.error(error); // log to console instead

        // TODO: better job of transforming error for user consumption
        console.log(`${operation} failed: ${error.message}`);

        // Let the app keep running by returning an empty result.
        return of(result as T);
      };
    }

  getPing() {
    return this.http.get(this.url + "ping", this.httpOptions)
    .pipe(
      tap(_ => console.log("ping")),
      catchError(this.handleError('error'))
    ).subscribe();
  }

  getCars(){
    return this.http.get(this.url + "api/cars", this.httpOptions)
    .pipe(
      tap(_ => console.log("cars")),
      catchError(this.handleError('error'))
    );
  }

  postCars(){
    var car = JSON.stringify({
      'brand': 'Mercedes',
      'model': 'C63',
      'color': 'Black',
      'licensePlate': 'LD456EZ',
      'owner': '/api/users/1',
      'fuel': '/api/fuels/1',
      'seatNumber': 5
    });
    return this.http.post(this.url + "api/cars", car ,this.httpOptions)
    .pipe(
      tap(_ => console.log("cars")),
      catchError(this.handleError('error'))
    );
  }

  updateCar(id: string){
    var car = JSON.stringify({
      'brand': 'Mercedes',
      'model': 'C63',
      'color': 'Black',
      'licensePlate': 'LD456EZ',
      'owner': '/api/users/1',
      'fuel': '/api/fuels/1',
      'seatNumber': 5
    });
    return this.http.put(this.url + "api/cars/" + id, car, this.httpOptions)
    .pipe(
      tap(_ => console.log("update car")),
      catchError(this.handleError('error'))
    );
  }

  deleteCar(id: number){
    return this.http.delete(this.url + "api/cars/" + id, this.httpOptions)
    .pipe(
      tap(_ => console.log("delete car")),
      catchError(this.handleError('error'))
    );
  }
}
