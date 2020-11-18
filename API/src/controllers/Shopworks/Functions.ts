import Axios, { AxiosResponse } from 'axios';
const fs = require('fs');

const config = require('dotenv').config({ path: './.env' });
const { CLIENT_ID, CLIENT_SECRET, CLIENT_URL } = config.parsed;

export interface IProducts {
  product: IProduct[];
}

interface IProduct {
  name: string;
  categoryId: string;
  categoryName: string;
  id: string;
  description: string;
  price: number;
  imgUrl: string;
  availableStock: number;
}

export interface IToken {
  access_token: string;
}

export default class Shopworks {
  async getOath(): Promise<IToken> {
    console.log(process.env.CLIENT_URL);
    const response: AxiosResponse<IToken> = await Axios({
      baseURL: CLIENT_URL,
      method: 'post',
      url: '/api/oauth/token',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'User-Agent': 'Shopware-Api-Toolbox',
      },
      data: {
        client_id: CLIENT_ID,
        client_secret: CLIENT_SECRET,
        grant_type: 'client_credentials',
      },
    });

    return response.data;
  }
  async getIds(): Promise<any> {
    const authToken: IToken = await this.getOath();

    const response = await Axios({
      baseURL: CLIENT_URL,
      method: 'get',
      url: '/api/v3/category',
      headers: {
        Authorization: `Bearer ${authToken.access_token}`,
      },
    });
    let categories = [];
    for (let i = 0; i < response.data.data.length; i++) {
      categories.push({
        id: response.data.data[i].id,
        name: response.data.data[i].name,
      });
    }
    return categories;
  }

  async getItems(): Promise<void> {
    const authToken = await this.getOath();
    const categories = await this.getIds();

    let products: any = [];
    let categoryData = [];

    for (let i = 1; i < categories.length; i++) {
      products = [];
      const data = await Axios({
        baseURL: CLIENT_URL,
        method: 'get',
        url: `/api/v3/category/${categories[i].id}/products?associations[media][]`,
        headers: {
          Authorization: `Bearer ${authToken.access_token}`,
        },
      });
      const category: Array<object> = await data.data.data;
      category.map((product: any) => {
        products.push({
          name: product.name,
          id: product.id,
          description: product.description,
          price: product.price[0].gross,
          imgUrl: product.media[0].media.url,
          availableStock: product.availableStock,
          categoryName: categories[i].name,
          categoryId: categories[i].id,
        });
      });
      categoryData.push({
        name: categories[i].name,
        id: categories[i].id,
        products: products,
      });
    }
    console.log(categoryData);

    const data = JSON.stringify(categoryData);
    fs.writeFileSync('./src/json/products.json', data);
    return;
  }
}
