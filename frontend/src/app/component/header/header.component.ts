import { Component, OnInit } from '@angular/core';
import { HttpServiceService } from '../../service/http/http-service.service';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.sass']
})
export class HeaderComponent implements OnInit {

  ngOnInit(): void {
    
  }
  // private httpService: HttpServiceService

  // private allCars: any

  // constructor(httpService: HttpServiceService) {
  //   this.httpService = httpService;
  // }

  // ngOnInit(): void {
  //   this.httpService.getPing();
    
  //   function randomIntFromInterval(min: number, max: number): number { // min and max included 
  //     return Math.floor(Math.random() * (max - min + 1) + min)
  //   }

  //   const that = this;
  //   this.httpService.getCars().subscribe(
  //     {
  //       next(res: any){
  //         var randomId = randomIntFromInterval(1, res["hydra:totalItems"])
  //         console.log(randomId);
  //         that.httpService.deleteCar(randomId).subscribe();
          
  //         return res;
  //       },
  //       error(err: any){
  //         console.log("Error ", console.log(err))
  //       }
  //     }
  //   );
    

  //   this.httpService.postCars().subscribe(
  //     (data: any ) => { 
  //       console.log(data.id); 
  //       this.httpService.updateCar(data.id).subscribe();
  //       return data;
  //       }
  //     );
    
}
