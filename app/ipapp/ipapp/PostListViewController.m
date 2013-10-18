//
//  PostListViewController.m
//  ipapp
//
//  Created by kelaocai on 13-10-1.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "PostListViewController.h"
#import "common.h"
#import "AFNetworking.h"
#import "PostListTableCell.h"


@interface PostListViewController ()

@end

@implementation PostListViewController

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.posts=nil;
    [self remoteGetPostList];
    

}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{

    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{

    // Return the number of rows in the section.
    if(self.posts!=nil){
        return [self.posts count];
    }else{
        return 0;
    }
    
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"Cell";
    PostListTableCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        
        
        cell = [[[PostListTableCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
        
        NSDictionary *post=[self.posts objectAtIndex:[indexPath row]];
        NSString *msg=[post objectForKey:@"message"];
        UIFont *font = [UIFont systemFontOfSize:14.0f];
        //设置内容块大小
        CGSize size=CGSizeMake(200.0f, MAXFLOAT);
        CGSize lable_size=[msg sizeWithFont:font constrainedToSize:size lineBreakMode:NSLineBreakByWordWrapping];
        [cell.message setFrame:CGRectMake(85, 10, 200.0f, lable_size.height)];
        [cell.messageBgView setFrame:CGRectMake(75, 5, 220.0f, lable_size.height+10.0f)];
        cell.message.text=msg;
        //设置头像
        //http://tongshibang.com/bbs/uc_server/data/avatar/000/00/00/01_avatar_small.jpg
        NSURL *avatar_url = [NSURL URLWithString:[post objectForKey:@"avatar"]];
        [cell.avatar setImageWithURL:avatar_url];
        
    
        
    }
    
    // Configure the cell...
    
    return cell;
}


- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    
    NSDictionary *post=[self.posts objectAtIndex:[indexPath row]];
    NSString *msg=[post objectForKey:@"message"];
    UIFont *font = [UIFont systemFontOfSize:14.0f];
    //设置内容块大小
    CGSize size=CGSizeMake(200.0f, MAXFLOAT);
    CGSize lable_size=[msg sizeWithFont:font constrainedToSize:size lineBreakMode:NSLineBreakByWordWrapping];
    return lable_size.height+42.0f;
};



-(void) remoteGetPostList{
    
    //1 读取远程板块数据
    NSString *BaseURLString=BASE_URL;
    NSString *weatherUrl = [NSString stringWithFormat:@"%@?c=forum&a=forum_post_list&tid=%@", BaseURLString,self.tid];
    NSURL *url = [NSURL URLWithString:weatherUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    // 2
    AFJSONRequestOperation *operation =
    [AFJSONRequestOperation JSONRequestOperationWithRequest:request
     // 3
                                                    success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
                                                        self.posts = (NSDictionary *)JSON;
                                                        [self.postTableView reloadData];
                                                    }
     // 4
                                                    failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
                                                        UIAlertView *av = [[UIAlertView alloc] initWithTitle:@"Error Retrieving Weather"
                                                                                                     message:[NSString stringWithFormat:@"%@",error]
                                                                                                    delegate:nil
                                                                                           cancelButtonTitle:@"OK" otherButtonTitles:nil];
                                                        [av show];
                                                    }];
    
    // 5
    [operation start];
    
}


/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/

/*
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
    }   
    else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}
*/

/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
}
*/

/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/

/*
#pragma mark - Table view delegate

// In a xib-based application, navigation from a table can be handled in -tableView:didSelectRowAtIndexPath:
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Navigation logic may go here, for example:
    // Create the next view controller.
    <#DetailViewController#> *detailViewController = [[<#DetailViewController#> alloc] initWithNibName:@"<#Nib name#>" bundle:nil];

    // Pass the selected object to the new view controller.
    
    // Push the view controller.
    [self.navigationController pushViewController:detailViewController animated:YES];
}
 
 */

- (void)dealloc {
    [_postTableView release];
    [super dealloc];
}
@end
