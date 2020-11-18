import {
  Controller,
  Get,
  Post,
  Response,
  Request,
  ContentType,
} from '@tsed/common';
const fs = require('fs');

//Functions
import Shopworks, { IProducts, IToken } from './Functions';
const shopworks = new Shopworks();

@Controller('/shopworks')
export class ShopworksController {
  @Get('/products')
  @ContentType('json')
  async getProducts(request: Request, response: Response): Promise<any> {
    const data = fs.readFileSync('./src/json/products.json');
    return data.toString();
  }
  @Get('/categories')
  async getCategories(request: Request, response: Response): Promise<object> {
    const categories: object = await shopworks.getIds();
    return categories;
  }
  @Post('/update')
  async updateProducts(request: Request, response: Response): Promise<string> {
    await shopworks.getItems();
    return 'Update Done!';
  }
}
